<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductSearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        return view('layouts.store');
    }
    public function suggest(Request $request)
    {
        $query = $request->input('q');

        $results = Product::search($query)
            ->take(5)
            ->get();

        return response()->json($results);
    }

    public function getProducts(Request $request, ProductSearchService $search)
    {
        $result = $search->search($request);

        return response()->json([
            'products' => $result['hits'],
            'pagination' => $result['pagination'],
            'categories' => Category::all(),
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

        return response()->json([
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'products' => $result['hits'],
            'pagination' => $result['pagination'],
        ]);
    }



}
