<div class="catalog__cards-wrapper" data-testid="catalog-products-grid">
    @forelse($products as $product)
        @include('blocks/card-product')
    @empty
        <p class="catalog__empty" data-testid="catalog-empty">
            По выбранным параметрам товары не найдены.
        </p>
    @endforelse
</div>

<div class="catalog-pagination__container" data-testid="catalog-pagination">
    {{ $products->onEachSide(1)->links('shop.product.partials.pagination') }}
</div>
