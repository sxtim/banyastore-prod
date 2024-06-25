<?php

namespace App\Services\Product;

use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    public function getProductsByOrderProducts(Collection $products): array
    {
        $newProducts = [];

        foreach ($products as $product) {
            $searchKey = array_search($product->id, array_column($newProducts, 'id'));
            if ($searchKey !== false) {
                $newProducts[$searchKey]['quantity']++;
                $newProducts[$searchKey]['price'] = $product->pivot->price * $newProducts[$searchKey]['quantity'];
                $newProducts[$searchKey]['oldPrice'] = $product->pivot->base_price * $newProducts[$searchKey]['quantity'];
                $newProducts[$searchKey]['discount'] = $product->pivot->discount * $newProducts[$searchKey]['quantity'];
            } else {
                $newProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'link' => route('products.detail', ['slug' => $product->slug]),
                    'price' => $product->pivot->price,
                    'oldPrice' => $product->pivot->base_price,
                    'discount' => $product->pivot->discount,
                    'image' => $product->getImageUrlAttribute(),
                    'quantity' => 1
                ];
            }
        }

        return $newProducts;
    }
}

