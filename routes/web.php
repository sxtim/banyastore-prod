<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {

    Route::middleware('role:admin')->group(function () {
        Route::prefix('backend')->name('backend.')->namespace('Backend')->group(function () {

            Route::get('/', [\App\Http\Controllers\Backend\BackendController::class, 'index'])->name('index');

            Route::get('/shop', [\App\Http\Controllers\Backend\ShopController::class, 'index'])->name('shop.index');

            //Продукты
            Route::prefix('product')->name('product.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\ProductController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\ProductController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\ProductController::class, 'store'])->name('store');
                Route::get('/edit/{product}', [\App\Http\Controllers\Backend\ProductController::class, 'edit'])->name('edit');
                Route::patch('/edit/{product}', [\App\Http\Controllers\Backend\ProductController::class, 'update'])->name('update');
            });
        });
    });

});

require __DIR__.'/auth.php';
