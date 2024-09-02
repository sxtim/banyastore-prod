<?php

namespace App\Services\Order;

use App\DTO\Order\NewOrderDto;
use App\Models\Order\Order;
use App\Models\Order\OrderProduct;
use App\Services\Basket\BasketInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderService
{
    private BasketInterface $basketService;

    public function __construct(BasketInterface $basket)
    {
        $this->basketService = $basket;
    }

    /**
     * @throws \Throwable
     */
    public function createOrder(NewOrderDto $newOrderDto): Order
    {
        $user = Auth::getUser();
        $orderBuilder = new OrderBuilder();
        $order = $orderBuilder
            ->setName($newOrderDto->getName())
            ->setPhone($newOrderDto->getPhone())
            ->setMail($newOrderDto->getMail())
            ->setUser($user)
            ->setDeliveryVariant($newOrderDto->getDeliveryVariant())
            ->setPaymentVariant($newOrderDto->getPaymentVariant())
            ->setDelivery($newOrderDto->getDeliveryDto())
            ->setBasketProducts($this->basketService->getBasket())
            ->createOrder();

        try {
            $order->save();

            return $order;
        } catch (\Exception $e) {
            if ($order->deliveries()->exists()) {
                $order->deliveries()->delete();
            }
            $order->delete();
            Log::error($e->getMessage());
            throw ($e);
        }
    }

    public function updatePrice(int $orderId, int $productId, float $price): void
    {
        OrderProduct::where('order_id', $orderId)
            ->where('product_id', $productId)
            ->update([
                'price' => $price
            ]);

        $order = Order::where('id', $orderId)->with(['products'])->first();
        $price = 0;
        foreach ($order->products as $itemProduct)
        {
            $price += $itemProduct->pivot->price;
        }
        $order->update([
            'price' => $price
        ]);
    }
}

