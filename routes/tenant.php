<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/', [SearchController::class, 'index'])->name('store.index');
    Route::get('/api/products/{slug}', [ProductController::class, 'show']);
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('store.suggest');
    Route::get('/api/products', [SearchController::class, 'getProducts'])->name('products.get');
    Route::get('/api/categories/{slug}', [SearchController::class, 'getCategoryProducts']);





    Route::get('{any}', function () {
        return view('layouts.store'); // или как называется твой blade-шаблон
    })->where('any', '.*');
    require __DIR__.'/auth.php';
});
