<?php

namespace App\Services\Basket;

use App\Models\Basket\SessionBasket;
use App\Models\Basket\SessionBasketProduct;
use App\Models\Shop\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SessionBasketService implements BasketInterface
{

    public function __construct(
    )
    {
    }

    private function userBasket(): SessionBasket
    {
        $userBasket = SessionBasket::where('session_uid','=', Session::get(SessionBasket::SESSION_UID_NAME))->first();
        if ($userBasket === null) {
            $userBasket = SessionBasket::create(
                [
                    'session_uid' => $this->createSessionUidCart()
                ]
            );
        }
        $userBasket->load(['products','products.product','products.product.discount']);
        return $userBasket;
    }

    private function createSessionUidCart(): string
    {
        $sessionUid = Str::random(40);
        Session::put(SessionBasket::SESSION_UID_NAME, $sessionUid);

        return $sessionUid;
    }

    public function getBasket(): Collection
    {
        $transformedCart = collect();

        $this
            ->userBasket()
            ->products
            ->each(function (SessionBasketProduct $basketProduct) use ($transformedCart) {

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
        return $this->userBasket()->products->sum(function (SessionBasketProduct $product) {
            return $product->quantity;
        });
    }

    public function add(int $productId): void
    {


        $cartItem = $this->userBasket()->products->firstWhere('product_id', $productId);

        if (!$cartItem) {
            $this->userBasket()->products()->create([
                'product_id' => $productId,
                'quantity' => 1
            ]);
        }
    }



    public function increment(int $productId): void
    {
        $cartItem = $this->userBasket()->products()->firstWhere('product_id', $productId);

        if (!$cartItem) {
            $this->add($productId);
        } else {
            $cartItem->update(['quantity' => $cartItem->quantity + 1]);
        }
    }

    public function decrement(int $productId): void
    {
        $cartItem = $this->userBasket()->products->firstWhere('product_id', $productId);

        if (!$cartItem) {
            return;
        }

        if ($cartItem->quantity - 1 > 0) {
            $cartItem->update(['quantity' => $cartItem->quantity - 1]);
        }

    }

    public function remove(int $productId): void
    {
        $usBasket = $this->userBasket();
        $usBasket->touch();
        $usBasket->touchOwners();

        $usBasket->products()->where('product_id', $productId)->delete();
    }

    public function update(Product $product, int $quantity): void
    {
        $cartItem = $this->userBasket()->products()->firstWhere('product_id', $product->id);

        if (!$cartItem) {
            $this->add($product->id);
        }

        $this->userBasket()->products()->where('product_id', $product->id)->update([
            'quantity' => $quantity
        ]);
    }


    public function destroy(): void
    {
        $usBasket = $this->userBasket();
        $usBasket->products()->delete();
        $usBasket->touch();
    }
}
