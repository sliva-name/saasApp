<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StoreController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn() => view('welcome'))->middleware('web');


Route::middleware(['auth'])->group(function () {
    Route::group(['prefix'=> 'stores'], function () {
        Route::get('/', [StoreController::class, 'index'])->name('stores.index');
        Route::get('/create', [StoreController::class, 'create'])->name('stores.create');
        Route::post('/store', [StoreController::class, 'store'])->name('stores.store');
        Route::get('/{store}', [StoreController::class, 'show'])->name('stores.show');
    });
    Route::group(['prefix'=> 'settings'], function () {
        Route::get('/', [StoreController::class, 'settings'])->name('settings.index');
    });

    // Админские маршруты для управления темами
    Route::group(['prefix' => 'admin/themes', 'middleware' => ['can:manage-themes']], function () {
        Route::get('/', [App\Http\Controllers\Admin\ThemeManagementController::class, 'index'])->name('admin.themes.index');
        Route::post('/install', [App\Http\Controllers\Admin\ThemeManagementController::class, 'install'])->name('admin.themes.install');
        Route::delete('/{packageName}', [App\Http\Controllers\Admin\ThemeManagementController::class, 'uninstall'])->name('admin.themes.uninstall');
        Route::post('/{packageName}/toggle', [App\Http\Controllers\Admin\ThemeManagementController::class, 'toggleActive'])->name('admin.themes.toggle');
        Route::put('/{packageName}', [App\Http\Controllers\Admin\ThemeManagementController::class, 'update'])->name('admin.themes.update');
        Route::post('/scan', [App\Http\Controllers\Admin\ThemeManagementController::class, 'scan'])->name('admin.themes.scan');
        Route::get('/{packageName}/export', [App\Http\Controllers\Admin\ThemeManagementController::class, 'export'])->name('admin.themes.export');
        Route::get('/logs', [App\Http\Controllers\Admin\ThemeManagementController::class, 'getLogs'])->name('admin.themes.logs');
    });



});

Route::get('/dashboard',[DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
