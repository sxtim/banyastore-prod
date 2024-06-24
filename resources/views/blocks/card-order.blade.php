<article class="order-card">
    <div class="order-card__top">
        <div class="order-card__top-wrapper">
            <div class="order-card__top-info">
                <a href="{{ route('personal.orders.detail', ['id' => $order->id]) }}" class="order-card__link">
                    <span class="order-card__title">
                        ЗАКАЗ № {{ $order->id }} от 21.11.2023г.
                    </span>
                </a>
                <div class="order-card__status">
                    <span class="order-card__status-delivery">Активен</span>
                </div>
            </div>
            <div class="order-card__total">
                <span class="order-card__header-sum">{{ number_format($order->price, 2, '.', ' ') }} ₽</span>
            </div>
        </div>
    </div>
    <div class="order-card__bottom">
        <div class="order-card__shipment-info">
            <span class="order-card__payment-info">
                {{ $order->paymentVariant->name }}
            </span>
            &nbsp;|&nbsp;
            <span class="order-card__shipment-type">
                {{ $order->deliveryVariant->name }}
            </span>
        </div>
        <div class="order-card__address">
            <span class="order-card__address-shipment">
                {{ $order->lastDelivery() ? $order->lastDelivery()->address : '' }}
            </span>
        </div>
    </div>
</article>
