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
                Route::post('/add-image', [\App\Http\Controllers\Backend\ProductController::class, 'addImage'])->name('add-image');
                Route::post('/delete-image', [\App\Http\Controllers\Backend\ProductController::class, 'deleteImage'])->name('delete-image');
            });

            //Категории
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\CategoryController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\CategoryController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\CategoryController::class, 'store'])->name('store');
                Route::get('/edit/{category}', [\App\Http\Controllers\Backend\CategoryController::class, 'edit'])->name('edit');
                Route::patch('/edit/{category}', [\App\Http\Controllers\Backend\CategoryController::class, 'update'])->name('update');
            });

            //Свойства
            Route::prefix('properties')->name('properties.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\PropertyController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\PropertyController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\PropertyController::class, 'store'])->name('store');
                Route::get('/edit/{property}', [\App\Http\Controllers\Backend\PropertyController::class, 'edit'])->name('edit');
                Route::patch('/edit/{property}', [\App\Http\Controllers\Backend\PropertyController::class, 'update'])->name('update');
            });

            //Значения свойств
            Route::prefix('property-values')->name('property-values.')->group(function () {
                Route::get('/{propertyId}', [\App\Http\Controllers\Backend\PropertyValueController::class, 'index'])->name('index');
                Route::get('/create/{propertyId}', [\App\Http\Controllers\Backend\PropertyValueController::class, 'create'])->name('create');
                Route::post('/create/{propertyId}', [\App\Http\Controllers\Backend\PropertyValueController::class, 'store'])->name('store');
                Route::get('/edit/{propertyValueId}', [\App\Http\Controllers\Backend\PropertyValueController::class, 'edit'])->name('edit');
                Route::patch('/edit/{propertyValueId}', [\App\Http\Controllers\Backend\PropertyValueController::class, 'update'])->name('update');
            });

            //Пользователи
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\UserController::class, 'index'])->name('index');
            });

            //Акции
            Route::prefix('actions')->name('actions.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\ActionController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\ActionController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\ActionController::class, 'store'])->name('store');
                Route::get('/edit/{actionId}', [\App\Http\Controllers\Backend\ActionController::class, 'edit'])->name('edit');
                Route::patch('/edit/{actionId}', [\App\Http\Controllers\Backend\ActionController::class, 'update'])->name('update');
                Route::post('/add-image', [\App\Http\Controllers\Backend\ActionController::class, 'addImage'])->name('add-image');
                Route::post('/delete-image', [\App\Http\Controllers\Backend\ActionController::class, 'deleteImage'])->name('delete-image');
            });

            //Новости
            Route::prefix('news')->name('news.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\NewsController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\NewsController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\NewsController::class, 'store'])->name('store');
                Route::get('/edit/{newsId}', [\App\Http\Controllers\Backend\NewsController::class, 'edit'])->name('edit');
                Route::patch('/edit/{newsId}', [\App\Http\Controllers\Backend\NewsController::class, 'update'])->name('update');
                Route::post('/add-image', [\App\Http\Controllers\Backend\NewsController::class, 'addImage'])->name('add-image');
                Route::post('/delete-image', [\App\Http\Controllers\Backend\NewsController::class, 'deleteImage'])->name('delete-image');
            });
        });
    });

});

require __DIR__.'/auth.php';
