<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductSearchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Collection;

class SearchController extends Controller
{
    protected $productSearchService;

    public function __construct(ProductSearchService $productSearchService)
    {
        $this->productSearchService = $productSearchService;
    }

    public function index(Request $request)
    {
        return View::make('layouts.store');
    }
    
    public function suggest(Request $request)
    {
        $query = trim($request->input('q'));
        

        $results = Product::search($query)
            ->take(5)
            ->get();

        return Response::json($results);
    }

    public function getProducts(Request $request)
    {
        $result = $this->productSearchService->search($request);

        // Делаем "дизъюнктивные" фасеты: считаем категории без учёта выбранной категории, 
        // но с учётом остальных фильтров (цена, наличие, текстовый запрос)
        $params = $request->all();
        unset($params['categories']);
        $disjunctiveRequest = new Request($params);
        $facetsResult = $this->productSearchService->search($disjunctiveRequest);
        $facetCategories = new Collection($facetsResult['facets']['category_id'] ?? []);

        // Строим список категорий с количеством из фасетов
        $categories = Category::orderBy('name')->get()->map(function ($category) use ($facetCategories) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => (int) ($facetCategories[$category->id] ?? 0),
            ];
        });

        return Response::json([
            'products' => $result['hits'],
            'pagination' => $result['pagination'],
            'categories' => $categories,
        ]);
    }

    public function getCategoryProducts(Request $request, string $slug, ProductSearchService $search)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        // Вписываем category_id в Request для фильтрации
        $request->merge([
            'category_id' => $category->id,
        ]);

        $result = $search->search($request);

        // Получаем бренды для этой категории
        $brands = new Collection();
        if (Product::first() && Product::first()->getAttribute('brand')) {
            $brands = Product::where('category_id', $category->id)
                ->selectRaw('brand as name, COUNT(*) as count')
                ->whereNotNull('brand')
                ->groupBy('brand')
                ->get()
                ->map(function ($brand, $index) {
                    return [
                        'id' => $index + 1,
                        'name' => $brand->name,
                        'count' => $brand->count
                    ];
                });
        }

        return Response::json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description ?? null,
            ],
            'products' => $result['hits'],
            'pagination' => $result['pagination'],
            'brands' => $brands,
        ]);
    }
}
