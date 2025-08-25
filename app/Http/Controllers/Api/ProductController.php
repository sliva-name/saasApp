<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

// Helper functions
if (!function_exists('response')) {
    function response($data = null, $status = 200, $headers = []) {
        return new \Illuminate\Http\JsonResponse($data, $status, $headers);
    }
}

if (!function_exists('tenant')) {
    function tenant($key = null) {
        $tenant = app(\Stancl\Tenancy\Contracts\Tenant::class);
        return $key ? $tenant?->$key : $tenant;
    }
}

class ProductController extends Controller
{
    /**
     * Получить список продуктов с фильтрацией и пагинацией
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'page' => 'integer|min:1',
            'per_page' => 'integer|min:1|max:100',
            'category_id' => 'integer|exists:categories,id',
            'min_price' => 'numeric|min:0',
            'max_price' => 'numeric|min:0',
            'in_stock' => 'boolean',
            'sort_by' => 'string|in:name,price,created_at,stock',
            'sort_direction' => 'string|in:asc,desc',
            'search' => 'string|max:255'
        ]);

        $query = Product::with(['category', 'media']);

        // Фильтрация по категории
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Фильтрация по цене
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Фильтрация по наличию
        if ($request->filled('in_stock')) {
            if ($request->boolean('in_stock')) {
                $query->where('stock', '>', 0);
            } else {
                $query->where('stock', '<=', 0);
            }
        }

        // Поиск по названию и описанию
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Сортировка
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Пагинация
        $perPage = $request->get('per_page', 12);
        $products = $query->paginate($perPage);

        return response()->json([
            'data' => $products->getCollection()->map(function ($product) {
                return $this->formatProduct($product);
            }),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
            ],
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ]
        ]);
    }

    /**
     * Получить конкретный продукт по ID или slug
     */
    public function show(Request $request, $identifier): JsonResponse
    {
        // Пытаемся найти по ID или slug
        $product = Product::with(['category', 'media'])
            ->where('id', $identifier)
            ->orWhere('slug', $identifier)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'data' => $this->formatProduct($product, true)
        ]);
    }

    /**
     * Поиск продуктов через MeiliSearch
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|max:255',
            'limit' => 'integer|min:1|max:100',
            'filters' => 'array',
            'filters.category_id' => 'integer|exists:categories,id',
            'filters.min_price' => 'numeric|min:0',
            'filters.max_price' => 'numeric|min:0',
            'filters.in_stock' => 'boolean'
        ]);

        $query = $request->q;
        $limit = $request->get('limit', 20);
        $filters = $request->get('filters', []);

        try {
            // Строим фильтры для MeiliSearch
            $searchFilters = [];
            
            if (isset($filters['category_id'])) {
                $searchFilters[] = "category_id = {$filters['category_id']}";
            }
            
            if (isset($filters['min_price'])) {
                $searchFilters[] = "price >= {$filters['min_price']}";
            }
            
            if (isset($filters['max_price'])) {
                $searchFilters[] = "price <= {$filters['max_price']}";
            }
            
            if (isset($filters['in_stock']) && $filters['in_stock']) {
                $searchFilters[] = "stock > 0";
            }
            
            // Добавляем фильтр по тенанту
            if (tenant()) {
                $searchFilters[] = "tenant_id = " . tenant()->id;
            }

            // Выполняем поиск
            $searchResults = Product::search($query)
                ->when(!empty($searchFilters), function ($search) use ($searchFilters) {
                    return $search->filter(implode(' AND ', $searchFilters));
                })
                ->take($limit)
                ->get();

            return response()->json([
                'data' => $searchResults->map(function ($product) {
                    return $this->formatProduct($product);
                }),
                'meta' => [
                    'query' => $query,
                    'total' => $searchResults->count(),
                    'limit' => $limit
                ]
            ]);

        } catch (\Exception $e) {
            // Fallback к обычному поиску по базе данных
            $products = Product::with(['category', 'media'])
                ->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->limit($limit)
                ->get();

            return response()->json([
                'data' => $products->map(function ($product) {
                    return $this->formatProduct($product);
                }),
                'meta' => [
                    'query' => $query,
                    'total' => $products->count(),
                    'limit' => $limit,
                    'fallback' => true
                ]
            ]);
        }
    }

    /**
     * Получить популярные продукты
     */
    public function popular(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:50'
        ]);

        $limit = $request->get('limit', 8);
        
        // Кешируем популярные продукты на 1 час
        $cacheKey = 'popular_products_' . (tenant() ? tenant()->id : 'global') . '_' . $limit;
        
        $products = Cache::remember($cacheKey, 3600, function () use ($limit) {
            // Пока что сортируем по дате создания, позже можно добавить счетчик просмотров
            return Product::with(['category', 'media'])
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });

        return response()->json([
            'data' => $products->map(function ($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    /**
     * Получить похожие продукты
     */
    public function similar(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:20'
        ]);

        $limit = $request->get('limit', 6);

        $similarProducts = Product::with(['category', 'media'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->where('stock', '>', 0)
            ->inRandomOrder()
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => $similarProducts->map(function ($product) {
                return $this->formatProduct($product);
            })
        ]);
    }

    /**
     * Получить фильтры для продуктов
     */
    public function filters(): JsonResponse
    {
        $cacheKey = 'product_filters_' . (tenant() ? tenant()->id : 'global');
        
        $filters = Cache::remember($cacheKey, 1800, function () {
            return [
                'categories' => Category::select('id', 'name', 'slug')
                    ->withCount('products')
                    ->having('products_count', '>', 0)
                    ->orderBy('name')
                    ->get(),
                    
                'price_range' => [
                    'min' => Product::min('price') ?? 0,
                    'max' => Product::max('price') ?? 0
                ],
                
                'stock_counts' => [
                    'in_stock' => Product::where('stock', '>', 0)->count(),
                    'out_of_stock' => Product::where('stock', '<=', 0)->count()
                ]
            ];
        });

        return response()->json($filters);
    }

    /**
     * Форматировать продукт для API ответа
     */
    private function formatProduct(Product $product, bool $detailed = false): array
    {
        $data = [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $product->price,
            'stock' => (int) $product->stock,
            'in_stock' => $product->stock > 0,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug
            ] : null,
            'image' => $product->getFirstMediaUrl('image') ?: null,
            'created_at' => $product->created_at?->toISOString(),
            'updated_at' => $product->updated_at?->toISOString()
        ];

        if ($detailed) {
            $data['description'] = $product->description;
            $data['images'] = $product->getMedia('image')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => $media->getUrl(),
                    'thumb' => $media->getUrl('thumb'),
                    'alt' => $media->name
                ];
            });
        }

        return $data;
    }
}
