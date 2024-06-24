<?php

namespace App\Services\Payment\Alfa;

use App\DTO\Payment\PaymentResponseDto;
use App\Services\Payment\PaymentInterface;
use Illuminate\Support\Facades\Http;

class AlfaService implements PaymentInterface
{
    public function __construct(
        private readonly string $userName,
        private readonly string $password,
        private readonly string $url
    )
    {
    }

    public function orderRegister(string $amount, int $orderNumber): PaymentResponseDto
    {
        $data = [
            'userName' => $this->userName,
            'password' => $this->password,
            'orderNumber' => $orderNumber,
            'amount' => (int) $amount * 100,
            'returnUrl' => route('order.success'),
            'dynamicCallbackUrl' => ''
        ];

        $dto = new PaymentResponseDto();
        $dto->setIsSuccess(false);
        $dto->setRequest($data);

        try {
            $response = Http::post(
                $this->url.'/payment/rest/register.do?'.http_build_query($data),
            )->collect()->toArray();

            if (isset($response['formUrl']) && $response['formUrl']) {
                $dto->setIsSuccess(true);
                $dto->setUrl($response['formUrl']);

                if (isset($response['orderId'])) {
                    $dto->setOrderNumberPaymentSystem($response['orderId']);
                }
            }

            $dto->setResponse($response);

        } catch (\Exception $e) {
            $dto->setResponse(['message' => $e->getMessage()]);
        }

        return $dto;
    }
}

