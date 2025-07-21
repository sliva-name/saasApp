<?php

declare(strict_types=1);

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
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('store.products.show');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('store.suggest');
    Route::get('/api/products', [SearchController::class, 'getProducts'])->name('products.get');
    require __DIR__.'/auth.php';
});
