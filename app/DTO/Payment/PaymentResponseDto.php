<?php

namespace App\DTO\Payment;

class PaymentResponseDto
{
    private bool $isSuccess;

    private ?string $url = null;

    private ?string $orderNumberPaymentSystem = null;

    private ?array $request = [];

    private ?array $response = [];

    public function setIsSuccess(bool $isSuccess): void
    {
        $this->isSuccess = $isSuccess;
    }

    public function getIsSuccess(): bool
    {
        return $this->isSuccess;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param array|null $request
     */
    public function setRequest(?array $request): void
    {
        $this->request = $request;
    }

    /**
     * @return array|null
     */
    public function getRequest(): ?array
    {
        return $this->request;
    }

    /**
     * @param array|null $response
     */
    public function setResponse(?array $response): void
    {
        $this->response = $response;
    }

    /**
     * @return array|null
     */
    public function getResponse(): ?array
    {
        return $this->response;
    }

    /**
     * @param string|null $orderNumberPaymentSystem
     */
    public function setOrderNumberPaymentSystem(?string $orderNumberPaymentSystem): void
    {
        $this->orderNumberPaymentSystem = $orderNumberPaymentSystem;
    }

    /**
     * @return string|null
     */
    public function getOrderNumberPaymentSystem(): ?string
    {
        return $this->orderNumberPaymentSystem;
    }

}
