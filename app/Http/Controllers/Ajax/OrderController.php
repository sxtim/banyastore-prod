<?php

namespace App\Http\Controllers\Ajax;

use App\DTO\Delivery\DeliveryDto;
use App\DTO\Order\NewOrderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\OrderRequest;
use App\Mail\MyMailer;
use App\Services\Basket\BasketInterface;
use Illuminate\Support\Facades\Log;
use App\Services\Order\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{

    /**
     * @throws \Throwable
     */
    public function store(
        OrderRequest $request,
        OrderService $orderService,
        BasketInterface $basket,
        MyMailer $myMailer
    ): JsonResponse
    {
        $deliveryDto = new DeliveryDto();
        $deliveryDto->setCityName($request->input('city_name'));
        $deliveryDto->setStreet($request->input('street'));
        $deliveryDto->setHouse($request->input('house'));

        $newOrderDto = new NewOrderDto();
        $newOrderDto->setName($request->input('name'));
        $newOrderDto->setPhone($request->input('phone'));
        $newOrderDto->setMail($request->input('mail'));
        $newOrderDto->setPaymentVariant($request->input('payment_variant_id'));
        $newOrderDto->setDeliveryVariant($request->input('delivery_variant_id'));
        $newOrderDto->setDeliveryDto($deliveryDto);

        try {
            $order = $orderService->createOrder($newOrderDto);
            $basket->destroy();
            $myMailer->sendEmail('opt@feringermsk.ru','Новый заказ','На сайте banyastore.ru новый заказ №'. $order->id);
            $myMailer->sendEmail('yaf123@mail.ru','Новый заказ','На сайте banyastore.ru новый заказ №'. $order->id);
            $myMailer->sendEmail($request->input('mail'),'Заказ №'.$order->id,'Спасибо за заказ на сайте banyastore.ru. Ваш заказ №'. $order->id. '. В ближайшее время мы с Вами свяжемся');

            return response()->json([
                'status' => 'success',
                'link' => route('order.success'),
                'orderId' => $order->id
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'status' => 'error'
            ]);
        }


    }
}
