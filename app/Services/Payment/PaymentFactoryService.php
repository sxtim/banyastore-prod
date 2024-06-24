<?php

namespace App\Services\Payment;

use App\Models\Payment\PaymentVariant;
use App\Services\Payment\Alfa\AlfaService;

class PaymentFactoryService
{
    public function __construct(
        private readonly AlfaService $alfaService
    )
    {
    }

    public function getService(string $variant): PaymentInterface
    {
        return match ($variant) {
            PaymentVariant::ALFA => $this->alfaService,
            default => throw new \Exception('Invalid payment variant'),
        };
    }
}

