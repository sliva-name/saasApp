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



});

Route::get('/dashboard',[DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/auth.php';
