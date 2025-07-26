<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
class CategoryController extends Controller
{
    public function index()
    {

    }

    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $products = $category->products()->latest()->paginate(12)->through(fn ($product) => [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => $product->price,
            'description' => $product->description,
            'image_url' => $product->getFirstMediaUrl('image'),
        ]);

        return response()->json([
            'category' => [
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ]
        ]);
    }

}
