<?php
use Diglactic\Breadcrumbs\Breadcrumbs;


Breadcrumbs::for('feed', function ($trail) {
    $trail->push('Главная страница', route('home'));
});


Breadcrumbs::for('category', function ($trail, $category) {
    $trail->parent('feed');
    $trail->push($category->name, route('products.by-category', ['slug' => $category->slug]));
});

Breadcrumbs::for('search', function ($trail) {
    $trail->parent('feed');
    $trail->push('Поиск', route('products.search'));
});

Breadcrumbs::for('product', function ($trail, $product) {
    $trail->parent('category', $product->category);
    $trail->push($product->name, route('products.detail', ['slug' => $product->slug]));
});

Breadcrumbs::for('personal', function ($trail) {
    $trail->parent('feed');
    $trail->push('Личный кабинет', route('personal.index'));
});

Breadcrumbs::for('favorites', function ($trail) {
    $trail->parent('personal');
    $trail->push('Избранное', route('personal.favorites'));
});

Breadcrumbs::for('orders', function ($trail) {
    $trail->parent('personal');
    $trail->push('Заказы', route('personal.orders'));
});

Breadcrumbs::for('order-detail', function ($trail, $order) {
    $trail->parent('orders');
    $trail->push('Заказ №'.$order->id, route('personal.orders.detail', ['id' => $order->id]));
});

Breadcrumbs::for('actions', function ($trail) {
    $trail->parent('feed');
    $trail->push('Акции', route('actions.index'));
});

Breadcrumbs::for('action-detail', function ($trail, $action) {
    $trail->parent('actions');
    $trail->push($action->name, route('actions.detail', ['slug' => $action->slug]));
});

Breadcrumbs::for('news', function ($trail) {
    $trail->parent('feed');
    $trail->push('Новости', route('news.index'));
});

Breadcrumbs::for('news-detail', function ($trail, $news) {
    $trail->parent('news');
    $trail->push($news->name, route('news.detail', ['slug' => $news->slug]));
});

