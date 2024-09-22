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
                Route::post('/delete-additional-image', [\App\Http\Controllers\Backend\ProductController::class, 'deleteAdditionalImage'])->name('delete-additional-image');
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

            //Баннеры
            Route::prefix('banner')->name('banner.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\BannerController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\BannerController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\BannerController::class, 'store'])->name('store');
                Route::get('/edit/{bannerId}', [\App\Http\Controllers\Backend\BannerController::class, 'edit'])->name('edit');
                Route::patch('/edit/{bannerId}', [\App\Http\Controllers\Backend\BannerController::class, 'update'])->name('update');
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

            //Скидки
            Route::prefix('discount')->name('discount.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\DiscountController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\DiscountController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\DiscountController::class, 'store'])->name('store');
                Route::get('/edit/{discountId}', [\App\Http\Controllers\Backend\DiscountController::class, 'edit'])->name('edit');
                Route::patch('/edit/{discountId}', [\App\Http\Controllers\Backend\DiscountController::class, 'update'])->name('update');
            });

            //Заказы
            Route::prefix('order')->name('order.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\OrderController::class, 'index'])->name('index');
                Route::get('/show/{id}', [\App\Http\Controllers\Backend\OrderController::class, 'show'])->name('show');
                Route::post('/update-user/{id}', [\App\Http\Controllers\Backend\OrderController::class, 'updateUser'])->name('update-user');
                Route::post('/update-status/{id}', [\App\Http\Controllers\Backend\OrderController::class, 'updateStatus'])->name('update-status');
                Route::post('/update-price/{id}', [\App\Http\Controllers\Backend\OrderController::class, 'updatePrice'])->name('update-price');
            });

            //Обратная связь
            Route::prefix('feedback')->name('feedback.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\FeedbackController::class, 'index'])->name('index');
                Route::get('/show/{id}', [\App\Http\Controllers\Backend\FeedbackController::class, 'show'])->name('show');
            });

            //Seo шаблоны
            Route::prefix('seo')->name('seo.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Backend\SeoController::class, 'index'])->name('index');
                Route::get('/create', [\App\Http\Controllers\Backend\SeoController::class, 'create'])->name('create');
                Route::post('/create', [\App\Http\Controllers\Backend\SeoController::class, 'store'])->name('store');
                Route::get('/edit/{templateId}', [\App\Http\Controllers\Backend\SeoController::class, 'edit'])->name('edit');
                Route::patch('/edit/{templateId}', [\App\Http\Controllers\Backend\SeoController::class, 'update'])->name('update');
            });
        });
    });

    //Личный кабинет
    Route::prefix('personal')->name('personal.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Personal\PersonalController::class, 'index'])->name('index');
        Route::post('/update/{id}', [\App\Http\Controllers\Personal\PersonalController::class, 'update'])->name('update');
        Route::post('/password/{id}', [\App\Http\Controllers\Personal\PersonalController::class, 'password'])->name('password');
        Route::get('/favorites', [\App\Http\Controllers\Personal\PersonalController::class, 'favorites'])->name('favorites');
        Route::get('/orders', [\App\Http\Controllers\Personal\OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{id}', [\App\Http\Controllers\Personal\OrderController::class, 'detail'])->name('orders.detail');
        Route::get('/orders/pay/{id}', [\App\Http\Controllers\Personal\OrderController::class, 'pay'])->name('orders.pay');
    });
});



//Публичная часть
Route::get('/categories', [\App\Http\Controllers\Shop\CategoryController::class, 'index'])->name('category.list');
Route::get('/category/{slug}', [\App\Http\Controllers\Shop\ProductController::class, 'byCategory'])->name('products.by-category');
Route::get('/product/{slug}', [\App\Http\Controllers\Shop\ProductController::class, 'detail'])->name('products.detail');
Route::get('/news', [\App\Http\Controllers\NewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [\App\Http\Controllers\NewsController::class, 'detail'])->name('news.detail');
Route::get('/basket', [\App\Http\Controllers\BasketController::class, 'index'])->name('basket.index');
Route::get('/search/', [\App\Http\Controllers\Shop\ProductController::class, 'search'])->name('products.search');
Route::get('/company/', [\App\Http\Controllers\CompanyController::class, 'index'])->name('company.index');
Route::get('/actions', [\App\Http\Controllers\ActionController::class, 'index'])->name('actions.index');
Route::get('/actions/{slug}', [\App\Http\Controllers\ActionController::class, 'detail'])->name('actions.detail');
Route::get('/three-d-projects', [\App\Http\Controllers\ThreeDProjectController::class, 'index'])->name('three-d-projects');
Route::get('/feedback', [\App\Http\Controllers\FeedBackController::class, 'index'])->name('feedback-form');
Route::get('/agree-text', [\App\Http\Controllers\IndexController::class, 'agreeText'])->name('agree-text');


//Route::get('/mail', [\App\Http\Controllers\MailController::class, 'send'])->name('mail.send');

//Аякс
Route::prefix('ajax')->name('ajax.')->group(function () {

    //Аякс-корзина
    Route::prefix('basket')->name('basket.')->group(function () {
        Route::post('/get-basket', [\App\Http\Controllers\Ajax\BasketController::class, 'getBasket'])->name('get-basket');
        Route::post('/get-count', [\App\Http\Controllers\Ajax\BasketController::class, 'getCount'])->name('get-count');
        Route::post('/add', [\App\Http\Controllers\Ajax\BasketController::class, 'add'])->name('add');
        Route::post('/remove', [\App\Http\Controllers\Ajax\BasketController::class, 'remove'])->name('remove');
        Route::post('/decrement', [\App\Http\Controllers\Ajax\BasketController::class, 'decrement'])->name('decrement');
        Route::post('/increment', [\App\Http\Controllers\Ajax\BasketController::class, 'increment'])->name('increment');
    });

    //Заказ
    Route::post('/order/store', [\App\Http\Controllers\Ajax\OrderController::class, 'store'])->name('order.store');

    //Избранное
    Route::post('/favorite/get-data', [\App\Http\Controllers\Ajax\FavoriteController::class, 'getData'])->name('favorite.get-data');
    Route::post('/favorite/product', [\App\Http\Controllers\Ajax\FavoriteController::class, 'product'])->name('favorite.product');

    //Отправка номера телефона
    Route::post('/send-phone', [\App\Http\Controllers\SmsController::class, 'sendPhone'])->name('send.phone');

    //Обратная связь
    Route::post('/feedback', [\App\Http\Controllers\Ajax\FeedbackController::class, 'sendData'])->name('feedback.send');
});


Route::prefix('callback')->name('callback.')->group(function () {
    Route::get('/alfa-pay', [\App\Http\Controllers\Callback\AlfaController::class, 'callback'])->name('alfa.pay');
});


//Успешный заказ
Route::get('/order/success', [\App\Http\Controllers\OrderController::class, 'success'])->name('order.success');

//Не успешный заказ
Route::get('/order/error', [\App\Http\Controllers\OrderController::class, 'error'])->name('order.error');


Route::get('/', [\App\Http\Controllers\IndexController::class, 'index'])->name('home');

require __DIR__.'/auth.php';
