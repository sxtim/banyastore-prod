<?php

namespace App\DTO\Order;

use App\DTO\Delivery\DeliveryDto;
use Illuminate\Support\Collection;

class NewOrderDto
{
    private string $name;

    private string $phone;

    private string $mail;

    private int $deliveryVariant;

    private int $paymentVariant;

    private DeliveryDto $deliveryDto;

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $mail
     */
    public function setMail(string $mail): void
    {
        $this->mail = $mail;
    }

    /**
     * @return string
     */
    public function getMail(): string
    {
        return $this->mail;
    }

    /**
     * @param DeliveryDto $deliveryDto
     */
    public function setDeliveryDto(DeliveryDto $deliveryDto): void
    {
        $this->deliveryDto = $deliveryDto;
    }

    /**
     * @return DeliveryDto
     */
    public function getDeliveryDto(): DeliveryDto
    {
        return $this->deliveryDto;
    }

    /**
     * @param int $deliveryVariant
     */
    public function setDeliveryVariant(int $deliveryVariant): void
    {
        $this->deliveryVariant = $deliveryVariant;
    }

    /**
     * @return int
     */
    public function getDeliveryVariant(): int
    {
        return $this->deliveryVariant;
    }

    /**
     * @param int $paymentVariant
     */
    public function setPaymentVariant(int $paymentVariant): void
    {
        $this->paymentVariant = $paymentVariant;
    }

    /**
     * @return int
     */
    public function getPaymentVariant(): int
    {
        return $this->paymentVariant;
    }


}
