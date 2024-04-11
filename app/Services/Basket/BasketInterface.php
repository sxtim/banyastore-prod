<?php

namespace App\Services\Basket;

use App\Models\Shop\Product;
use Illuminate\Support\Collection;

interface BasketInterface
{
    /**
     * Получает корзину товаров
     */
    public function getBasket(): Collection;

    /**
     * Получает общее количество товаров в корзине
     */
    public function getCount(): int;

    /**
     * Добавляет товар в корзину
     */
    public function add(int $productId): void;

    /**
     * Прибавляет 1 единицу товара в корзине
     */
    public function increment(int $productId): void;

    /**
     * Убавляет 1 единицу товара в корзине
     */
    public function decrement(int $productId): void;

    /**
     *  Меняет количество товара в корзине
     */
    public function update(Product $product, int $quantity): void;

    /**
     * Удаляет товар из корзины
     */
    public function remove(int $productId): void;

    /**
     * Удаляет все товары из корзины
     */
    public function destroy(): void;
}
