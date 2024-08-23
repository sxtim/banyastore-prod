<?php

namespace App\Services\Payment\Alfa;

use App\DTO\Payment\PaymentResponseDto;
use App\Models\Order\Order;
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

    public function orderRegister(string $amount, Order $order): PaymentResponseDto
    {
        $order->load(['products']);
        $items = [];
        foreach ($order->listProducts() as $productList) {
            $items[] = [
                'positionId' => $productList['order_product_id'],
                'name' => $productList['name'],
                'quantity' => [
                    'value' => $productList['quantity'],
                    'measure' => 0
                ],
                'itemCode' => 'N'.$productList['product_id'],
                'tax' => [
                    'taxType' => 0
                ],
                'itemPrice' => (int) $productList['price'] * 100,
            ];
        }

        $orderBundle = [
            'customerDetails' => [
                'email' => 'skostait@gmail.com'
            ],
            'cartItems' => [
                'items' => $items
            ]
        ];

        $data = [
            'userName' => $this->userName,
            'password' => $this->password,
            'orderNumber' => 'T'.$order->id,
            'amount' => (int) $amount * 100,
            'returnUrl' => route('order.success'),
            'dynamicCallbackUrl' => '',
            'orderBundle' => json_encode($orderBundle, JSON_UNESCAPED_UNICODE),
            'taxSystem' => 2
        ];


        $dto = new PaymentResponseDto();
        $dto->setIsSuccess(false);
        $dto->setRequest($data);

        $response = Http::post(
            $this->url.'/payment/rest/register.do?'.http_build_query($data),
        )->collect()->toArray();

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

