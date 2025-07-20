<?php

namespace App\Http\Controllers;

use App\Helpers\TenantContext;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(12);

        return view('storefront.home', compact('products'));
    }
}
