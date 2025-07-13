<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {

    }

    public function show(Product $product)
    {
        return view('storefront.product', compact('product'));
    }
}
