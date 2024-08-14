<?php

namespace App\Services\Payment;

use App\DTO\Payment\PaymentResponseDto;
use App\Models\Order\Order;

interface PaymentInterface
{
    /**
     * Регистрируем заказ
     */
    public function orderRegister(string $amount, Order $order): PaymentResponseDto;
}
