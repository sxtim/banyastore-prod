<?php

namespace App\Services\Order;


use App\DTO\Delivery\DeliveryDto;
use App\Models\Order\Order;

use App\Models\Order\OrderStatus\OrderStatus;
use App\Models\User;
use App\Services\Basket\BasketItem;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class OrderBuilder
{
    private Order $order;
    private Collection $basketProducts;
    private DeliveryDto $delivery;
    private int $paymentVariant;
    private int $deliveryVariant;
    private ?int $userId = null;
    private string $name;
    private string $mail;
    private string $phone;

    public function __construct()
    {
        $this->order = new Order();
        $this->basketProducts = collect();
    }


    /**
     * @throws Exception
     */
    public function setBasketProducts(Collection $basketProducts): OrderBuilder
    {
        if ($basketProducts->isEmpty()) {
            throw new Exception('Basket is Empty');
        }

        $this->basketProducts = $basketProducts;

        return clone $this;
    }

    public function setDelivery(DeliveryDto $delivery): OrderBuilder
    {
        $this->delivery = $delivery;

        return clone $this;
    }

    public function setPaymentVariant(int $paymentVariant): OrderBuilder
    {
        $this->paymentVariant = $paymentVariant;

        return clone $this;
    }

    public function setDeliveryVariant(int $deliveryVariant): OrderBuilder
    {
        $this->deliveryVariant = $deliveryVariant;

        return clone $this;
    }

    public function setUser(?User $user): OrderBuilder
    {
        $this->userId = $user?->id;

        return clone $this;
    }

    public function setName(string $name): OrderBuilder
    {
        $this->name = $name;

        return clone $this;
    }

    public function setPhone(string $phone): OrderBuilder
    {
        $this->phone = $phone;

        return clone $this;
    }

    public function setMail(string $mail): OrderBuilder
    {
        $this->mail = $mail;

        return clone $this;
    }

    /**
     * @throws Throwable
     */
    public function createOrder(): Order
    {
        $order = null;

        $this->order->user_id = $this->userId;
        $this->order->status_id = OrderStatus::where('value_status', OrderStatus::STATUS_CREATE_VALUE)->first()->id;
        $this->order->name = $this->name;
        $this->order->phone = $this->phone;
        $this->order->mail = $this->mail;
        $this->order->payment_variant_id = $this->paymentVariant;
        $this->order->delivery_variant_id = $this->deliveryVariant;

        $products = [];
        $totalPrice = 0;

        /* @var BasketItem $basketItem */
        foreach ($this->basketProducts as $basketItem) {
            for ($i = 1; $i <= $basketItem->getQuantity(); $i++) {
                $totalPrice += $basketItem->getPrice();
                $products[] = [
                    'product_id' => $basketItem->getProductId(),
                    'price' => $basketItem->getPrice(),
                    'base_price' => $basketItem->getOldPrice(),
                    'discount' => $basketItem->getOldPrice() > $basketItem->getPrice() ? $basketItem->getOldPrice() - $basketItem->getPrice() : 0,
                ];
            }
        }

        $this->order->price = $totalPrice;

        DB::transaction(function () use (&$order, $products) {
            $order = Order::create($this->order->getAttributes());

            $order->products()->attach($products);

            $order->deliveries()->create([
                'city_name' => $this->delivery->getCityName(),
                'street' => $this->delivery->getStreet(),
                'house' => $this->delivery->getHouse(),
                'address' => $this->delivery->getCityName().', '.$this->delivery->getStreet().', '.$this->delivery->getHouse()
            ]);

            $order->save();
        });

        return $order;
    }
}
