<?php

declare(strict_types=1);

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
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

    // API маршруты должны быть ДО catch-all маршрута
    Route::prefix('api')->group(function () {
        // Существующие API маршруты
        Route::get('/products/{slug}', [ProductController::class, 'show']);
        Route::get('/products', [SearchController::class, 'getProducts'])->name('products.get');
        Route::get('/categories/{slug}', [SearchController::class, 'getCategoryProducts']);
        
        // Пользователь и CSRF
        Route::get('/me', function (Request $request) {
            $user = $request->user();
            if (!$user) {
                return Response::json(['message' => 'Unauthenticated'], 401);
            }
            return Response::json($user);
        });
        
        Route::get('/csrf-token', function () {
            return Response::json(['token' => Session::token()]);
        });

        // API для продуктов
        Route::prefix('products')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\ProductController::class, 'index']);
            Route::get('/search', [App\Http\Controllers\Api\ProductController::class, 'search']);
            Route::get('/popular', [App\Http\Controllers\Api\ProductController::class, 'popular']);
            Route::get('/filters', [App\Http\Controllers\Api\ProductController::class, 'filters']);
            Route::get('/{identifier}', [App\Http\Controllers\Api\ProductController::class, 'show']);
            Route::get('/{product}/similar', [App\Http\Controllers\Api\ProductController::class, 'similar']);
        });

        // API для категорий
        Route::prefix('categories')->group(function () {
            Route::get('/', [App\Http\Controllers\Api\CategoryController::class, 'index']);
            Route::get('/tree', [App\Http\Controllers\Api\CategoryController::class, 'tree']);
            Route::get('/popular', [App\Http\Controllers\Api\CategoryController::class, 'popular']);
            Route::get('/{identifier}', [App\Http\Controllers\Api\CategoryController::class, 'show']);
            Route::get('/{identifier}/breadcrumbs', [App\Http\Controllers\Api\CategoryController::class, 'breadcrumbs']);
        });

        // API для работы с темами
        Route::get('/tenant/active-theme', [App\Http\Controllers\Api\ThemeController::class, 'getActiveTenant']);
        Route::get('/theme/config', [App\Http\Controllers\Api\ThemeController::class, 'getConfig']);
        Route::put('/theme/config', [App\Http\Controllers\Api\ThemeController::class, 'updateConfig']);
        Route::get('/themes', [App\Http\Controllers\Api\ThemeController::class, 'index']);
        Route::get('/themes/{packageName}', [App\Http\Controllers\Api\ThemeController::class, 'show'])
            ->where('packageName', '.*'); // Разрешаем слэши в имени пакета
        Route::get('/themes/{packageName}/compatibility', [App\Http\Controllers\Api\ThemeController::class, 'checkCompatibility'])
            ->where('packageName', '.*');
        Route::get('/themes/{packageName}/preview', [App\Http\Controllers\Api\ThemeController::class, 'getPreview'])
            ->where('packageName', '.*');
        Route::post('/themes/activate', [App\Http\Controllers\Api\ThemeController::class, 'activate']);
        Route::get('/theme/components', [App\Http\Controllers\Api\ThemeController::class, 'getComponents']);
    });

    // Обычные веб маршруты
    Route::get('/', [SearchController::class, 'index'])->name('store.index');
    Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('store.suggest');

    // Auth маршруты
    require __DIR__.'/auth.php';

    // Catch-all маршрут должен быть ПОСЛЕДНИМ
    Route::get('{any}', function () {
        return View::make('layouts.store'); // или как называется твой blade-шаблон
    })->where('any', '.*');
});