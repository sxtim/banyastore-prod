<?php

namespace App\Services\Basket;

class BasketItem
{
    public int $productId;

    public int $quantity;

    public int $maxQuantity;

    public float $price;

    public float $discount;

    public float $oldPrice;

    public string $name;

    public string $image;

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return int
     */
    public function getMaxQuantity(): int
    {
        return $this->maxQuantity;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }


    public function getOldPrice(): float
    {
        return $this->oldPrice;
    }
}
