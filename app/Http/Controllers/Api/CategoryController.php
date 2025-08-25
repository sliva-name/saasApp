<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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

class CategoryController extends Controller
{
    /**
     * Получить список всех категорий с продуктами
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'with_products' => 'boolean',
            'only_with_products' => 'boolean',
            'parent_id' => 'nullable|integer|exists:categories,id'
        ]);

        $query = Category::query();

        // Фильтр по родительской категории
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->parent_id);
        }

        // Загружаем количество продуктов
        $query->withCount('products');

        // Фильтр только категории с продуктами
        if ($request->boolean('only_with_products')) {
            $query->having('products_count', '>', 0);
        }

        // Подгружаем продукты если нужно
        if ($request->boolean('with_products')) {
            $query->with(['products' => function ($q) {
                $q->where('stock', '>', 0)
                  ->orderBy('created_at', 'desc')
                  ->limit(8);
            }]);
        }

        $categories = $query->orderBy('sorting')
                           ->orderBy('name')
                           ->get();

        return response()->json([
            'data' => $categories->map(function ($category) use ($request) {
                return $this->formatCategory($category, $request->boolean('with_products'));
            })
        ]);
    }

    /**
     * Получить конкретную категорию
     */
    public function show(Request $request, $identifier): JsonResponse
    {
        $request->validate([
            'with_products' => 'boolean',
            'products_limit' => 'integer|min:1|max:100'
        ]);

        // Ищем по ID или slug
        $query = Category::where('id', $identifier)
                        ->orWhere('slug', $identifier);

        // Подгружаем продукты если нужно
        if ($request->boolean('with_products')) {
            $limit = $request->get('products_limit', 20);
            $query->with(['products' => function ($q) use ($limit) {
                $q->where('stock', '>', 0)
                  ->with(['media'])
                  ->orderBy('created_at', 'desc')
                  ->limit($limit);
            }]);
        }

        $category = $query->withCount('products')->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json([
            'data' => $this->formatCategory($category, $request->boolean('with_products'), true)
        ]);
    }

    /**
     * Получить дерево категорий
     */
    public function tree(): JsonResponse
    {
        $cacheKey = 'categories_tree_' . (tenant() ? tenant()->id : 'global');
        
        $tree = Cache::remember($cacheKey, 1800, function () {
            return $this->buildCategoryTree();
        });

        return response()->json([
            'data' => $tree
        ]);
    }

    /**
     * Получить хлебные крошки для категории
     */
    public function breadcrumbs($identifier): JsonResponse
    {
        $category = Category::where('id', $identifier)
                           ->orWhere('slug', $identifier)
                           ->first();

        if (!$category) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        $breadcrumbs = $this->buildBreadcrumbs($category);

        return response()->json([
            'data' => $breadcrumbs
        ]);
    }

    /**
     * Получить популярные категории
     */
    public function popular(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'integer|min:1|max:20'
        ]);

        $limit = $request->get('limit', 8);
        
        $cacheKey = 'popular_categories_' . (tenant() ? tenant()->id : 'global') . '_' . $limit;
        
        $categories = Cache::remember($cacheKey, 3600, function () use ($limit) {
            return Category::withCount('products')
                          ->having('products_count', '>', 0)
                          ->orderBy('products_count', 'desc')
                          ->orderBy('name')
                          ->limit($limit)
                          ->get();
        });

        return response()->json([
            'data' => $categories->map(function ($category) {
                return $this->formatCategory($category);
            })
        ]);
    }

    /**
     * Построить дерево категорий
     */
    private function buildCategoryTree($parentId = null): array
    {
        $categories = Category::where('parent_id', $parentId)
                             ->withCount('products')
                             ->orderBy('sorting')
                             ->orderBy('name')
                             ->get();

        $tree = [];

        foreach ($categories as $category) {
            $node = $this->formatCategory($category);
            
            // Рекурсивно получаем дочерние категории
            $children = $this->buildCategoryTree($category->id);
            if (!empty($children)) {
                $node['children'] = $children;
                $node['has_children'] = true;
            } else {
                $node['has_children'] = false;
            }

            $tree[] = $node;
        }

        return $tree;
    }

    /**
     * Построить хлебные крошки
     */
    private function buildBreadcrumbs(Category $category): array
    {
        $breadcrumbs = [];
        $current = $category;

        while ($current) {
            array_unshift($breadcrumbs, [
                'id' => $current->id,
                'name' => $current->name,
                'slug' => $current->slug
            ]);
            $current = $current->parent;
        }

        // Добавляем корневую ссылку
        array_unshift($breadcrumbs, [
            'id' => null,
            'name' => 'Все товары',
            'slug' => 'all'
        ]);

        return $breadcrumbs;
    }

    /**
     * Форматировать категорию для API ответа
     */
    private function formatCategory(Category $category, bool $withProducts = false, bool $detailed = false): array
    {
        $data = [
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'parent_id' => $category->parent_id,
            'products_count' => $category->products_count ?? 0,
            'sorting' => $category->sorting
        ];

        if ($detailed) {
            $data['created_at'] = $category->created_at?->toISOString();
            $data['updated_at'] = $category->updated_at?->toISOString();
            
            // Добавляем родительскую категорию
            if ($category->parent) {
                $data['parent'] = [
                    'id' => $category->parent->id,
                    'name' => $category->parent->name,
                    'slug' => $category->parent->slug
                ];
            }
            
            // Добавляем дочерние категории
            $data['children'] = $category->children->map(function ($child) {
                return [
                    'id' => $child->id,
                    'name' => $child->name,
                    'slug' => $child->slug,
                    'products_count' => $child->products()->count()
                ];
            });
        }

        if ($withProducts && $category->relationLoaded('products')) {
            $data['products'] = $category->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => (float) $product->price,
                    'stock' => (int) $product->stock,
                    'image' => $product->getFirstMediaUrl('image') ?: null
                ];
            });
        }

        return $data;
    }
}
