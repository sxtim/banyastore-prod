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

