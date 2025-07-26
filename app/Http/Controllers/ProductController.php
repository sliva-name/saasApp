<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {

    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return new ProductResource($product);
    }

}
