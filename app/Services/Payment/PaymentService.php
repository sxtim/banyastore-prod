<?php

namespace App\Services\Payment;

use App\DTO\Payment\PaymentResponseDto;
use App\Models\Order\Order;
use App\Models\Payment\Payment;
use App\Models\Payment\PaymentLog;

class PaymentService
{
    public function __construct(
        private readonly PaymentFactoryService $paymentFactoryService
    )
    {
    }

    public function pay(
        Order $order
    ): PaymentResponseDto
    {
        try {
            $paymentResponseDto = $this->paymentFactoryService
                ->getService($order->paymentVariant->slug)
                ->orderRegister($order->price, $order);

            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->price,
                'payment_url' => $paymentResponseDto->getUrl(),
                'order_number_payment_system' => $paymentResponseDto->getOrderNumberPaymentSystem()
            ]);

        } catch (\Exception $e) {
            $paymentResponseDto = new PaymentResponseDto();
            $paymentResponseDto->setIsSuccess(false);
            $paymentResponseDto->setResponse(['message' => $e->getMessage()]);
        }

        PaymentLog::create([
            'order_id' => $order->id,
            'method' => 'orderRegister',
            'response' => $paymentResponseDto->getResponse(),
            'request_params' => $paymentResponseDto->getRequest()
        ]);

        return $paymentResponseDto;
    }
}

