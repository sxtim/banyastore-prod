<?php

namespace App\Services\Payment;

use App\DTO\Payment\PaymentResponseDto;

interface PaymentInterface
{
    /**
     * Регистрируем заказ
     */
    public function orderRegister(string $amount, int $orderNumber): PaymentResponseDto;
}
