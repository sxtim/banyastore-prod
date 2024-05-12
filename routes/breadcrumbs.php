<?php
use Diglactic\Breadcrumbs\Breadcrumbs;


Breadcrumbs::for('feed', function ($trail) {
    $trail->push('Главная страница', route('home'));
});


Breadcrumbs::for('category', function ($trail, $category) {
    $trail->parent('feed');
    $trail->push($category->name, route('products.by-category', ['slug' => $category->slug]));
});

Breadcrumbs::for('product', function ($trail, $product) {
    $trail->parent('category', $product->category);
    $trail->push($product->name, route('products.detail', ['slug' => $product->slug]));
});

Breadcrumbs::for('personal', function ($trail) {
    $trail->parent('feed');
    $trail->push('Личный кабинет', route('personal.index'));
});

