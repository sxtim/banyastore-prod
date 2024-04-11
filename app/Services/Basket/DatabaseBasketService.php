<?php

namespace App\Services\Basket;

use App\Models\Basket\BasketProduct;
use App\Models\Shop\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class DatabaseBasketService implements BasketInterface
{

    /**
     * @throws \Exception
     */
    public function getBasket(): Collection
    {
        $user = Auth::getUser();

        $transformedCart = collect();

        $user
            ->basket
            ->products()
            ->with(['product.discount'])
            ->each(function (BasketProduct $basketProduct) use ($transformedCart) {

                $discount = 0;
                if ($basketProduct->product->getCurrentPrice() < $basketProduct->product->price) {
                    $discount = $basketProduct->product->price - $basketProduct->product->getCurrentPrice();
                }

                $basketItem = new BasketItem();

                $basketItem->basket_product_id = $basketProduct->id;
                $basketItem->basket_id = $basketProduct->basket_id;
                $basketItem->quantity = $basketProduct->quantity;
                $basketItem->price = $basketProduct->product->getCurrentPrice();
                $basketItem->oldPrice = $basketProduct->product->price;
                $basketItem->discount = $discount;
                $basketItem->name = $basketProduct->product->name;
                $basketItem->image = $basketProduct->product->getImageUrlAttribute();

                $transformedCart->push($basketItem);
            });

        return $transformedCart->sortBy('name');
    }

    public function getCount(): int
    {
        $cart = Auth::getUser()->basket->products;
        return $cart->sum(function (BasketProduct $basketProduct) {
            return $basketProduct->quantity;
        });
    }

    public function add(int $productId): void
    {
        $user = Auth::getUser();

        $cartItem = $user->basket->products->firstWhere('product_id', $productId);

        $product = Product::find($productId);

        if (!$cartItem && $product->quantity > 0) {
            $user->basket->products()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
    }

    public function increment(int $productId): void
    {
        $user = Auth::getUser();

        $cartItem = $user->basket->products()->firstWhere('product_id', $productId);

        $product = Product::find($productId);

        if (!$cartItem && $product->quantity > 0) {
            $this->add($productId);
        }
        if ($product->quantity > $cartItem->quantity) {
            $cartItem->update(['quantity' => $cartItem->quantity + 1]);
        }
    }

    public function decrement(int $productId): void
    {
        $user = Auth::getUser();

        $cartItem = $user->basket->products->firstWhere('product_id', $productId);

        if (!$cartItem) {
            return ;
        }

        if ($cartItem->quantity - 1 > 0) {
            $cartItem->update(['quantity' => $cartItem->quantity - 1]);
        }

    }

    public function remove(int $productId): void
    {
        $user = Auth::getUser();
    //    $user->basket->touch();
    //    $user->basket->touchOwners();

        $user->basket->products()->where('product_id', $productId)->delete();
    }

    public function update(Product $product, int $quantity): void
    {
        $user = Auth::getUser();

        $cartItem = $user->basket->products()->firstWhere('product_id', $product->id);

        if (!$cartItem) {
            $this->add($product->id);
        }

        $user->basket->products()->where('product_id', $product->id)->update([
            'quantity' => $quantity
        ]);
    }

    public function destroy(): void
    {
        $user = Auth::getUser();

        $user->basket->products()->delete();
        $user->basket->touch();
    }
}

