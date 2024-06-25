<article class="card-product-order">
    <img class="card-product-order__picture" src="{{ $product['image'] }}" alt="{{ $product['name'] }}">
    <div class="card-product-order__desc">
        <h2 class="card-product-order__title">
            {{ $product['name'] }}
        </h2>
        <div class="card-product-order__price-container">
            <span class="card-product-order__price">{{ $product['price'] }} ₽</span>
            @if ($product['oldPrice'] > $product['price'])
                <span class="card-product-order__price-old">{{ $product['oldPrice'] }} ₽</span>
            @endif
        </div>
        <div class="card-product-order__count count">
            {{ $product['quantity'] }}шт.
        </div>
    </div>
</article>
