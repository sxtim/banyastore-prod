@php
    $selectedProperties = collect($filters['properties'] ?? [])
        ->mapWithKeys(fn ($values, $propertyId) => [(string) $propertyId => array_map('strval', $values)]);
    $propertiesCollection = collect($properties);
    $featuredIds = collect($filterUi['featured_property_ids'])->map(fn ($id) => (string) $id);
    $alwaysOpenIds = collect($filterUi['always_open_property_ids'])->map(fn ($id) => (string) $id);
    $primaryProperties = $featuredIds
        ->map(fn ($id) => $propertiesCollection->first(fn ($property) => (string) $property['id'] === $id))
        ->filter()
        ->values();

    if ($primaryProperties->isEmpty()) {
        $primaryProperties = $propertiesCollection->take(6);
    }

    $primaryIds = $primaryProperties->pluck('id')->map(fn ($id) => (string) $id);
    $additionalProperties = $propertiesCollection
        ->reject(fn ($property) => $primaryIds->contains((string) $property['id']))
        ->values();
    $brandPropertyId = $filterUi['brand_property_id'] ?? null;
    $brandProperty = $primaryProperties->first(
        fn ($property) => (string) $property['id'] === (string) $brandPropertyId
    );
    $primaryProperties = $primaryProperties
        ->reject(fn ($property) => (string) $property['id'] === (string) $brandPropertyId)
        ->values();
    $activeFilterCount = $selectedProperties->flatten()->count()
        + (($selectedPrice['min'] ?? null) !== null || ($selectedPrice['max'] ?? null) !== null ? 1 : 0);
@endphp

<div class="catalog-filter-fallback" data-testid="catalog-filters-fallback">
    <div class="catalog-filter-panel">
        <header class="catalog-filter-panel__header">
            <div>
                <h2 class="catalog-filter-panel__title">Фильтры</h2>
                <span class="catalog-filter-panel__result-count">
                    {{ $resultCount }} {{ trans_choice('товар|товара|товаров', $resultCount) }}
                </span>
            </div>
            @if($activeFilterCount > 0)
                <a class="catalog-filter-panel__reset" href="{{ url()->current() }}">Сбросить</a>
            @endif
        </header>

        <div class="catalog-filter-panel__scroll">
            @if($brandProperty)
                @include('shop.product.partials.filter-group-fallback', [
                    'property' => $brandProperty,
                    'alwaysOpen' => $alwaysOpenIds->contains((string) $brandProperty['id']),
                ])
            @endif

            <details class="catalog-filter-group" open>
                <summary class="catalog-filter-group__trigger">
                    <span class="catalog-filter-group__name">Цена</span>
                    <span class="catalog-filter-group__chevron" aria-hidden="true">⌄</span>
                </summary>
                <div class="catalog-filter-group__content">
                    <div class="catalog-price-filter__inputs">
                        <label>
                            <span>От</span>
                            <input
                                type="number"
                                name="price_min"
                                min="{{ $priceBounds['min'] }}"
                                max="{{ $priceBounds['max'] }}"
                                value="{{ $selectedPrice['min'] ?? '' }}"
                                placeholder="{{ $priceBounds['min'] }}"
                            >
                        </label>
                        <label>
                            <span>До</span>
                            <input
                                type="number"
                                name="price_max"
                                min="{{ $priceBounds['min'] }}"
                                max="{{ $priceBounds['max'] }}"
                                value="{{ $selectedPrice['max'] ?? '' }}"
                                placeholder="{{ $priceBounds['max'] }}"
                            >
                        </label>
                    </div>
                </div>
            </details>

            @foreach($primaryProperties as $property)
                @include('shop.product.partials.filter-group-fallback', [
                    'property' => $property,
                    'alwaysOpen' => $alwaysOpenIds->contains((string) $property['id']),
                ])
            @endforeach

            @if($additionalProperties->isNotEmpty())
                <details class="catalog-filter-fallback__additional">
                    <summary class="catalog-filter-panel__additional">
                        Все характеристики ({{ $additionalProperties->count() }})
                        <span class="catalog-filter-group__chevron" aria-hidden="true">⌄</span>
                    </summary>
                    @foreach($additionalProperties as $property)
                        @include('shop.product.partials.filter-group-fallback', [
                            'property' => $property,
                            'alwaysOpen' => $alwaysOpenIds->contains((string) $property['id']),
                        ])
                    @endforeach
                </details>
            @endif
        </div>

        <footer class="catalog-filter-panel__footer">
            <button class="catalog__filter-submit btn btn-medium" type="submit">
                @if($resultCount > 0)
                    ПОКАЗАТЬ {{ $resultCount }} {{ trans_choice('товар|товара|товаров', $resultCount) }}
                @else
                    НЕТ ТОВАРОВ
                @endif
            </button>
        </footer>
    </div>
</div>
<script>
    (function (script) {
        var fallback = script.previousElementSibling;

        if (!fallback || !window.matchMedia('(min-width: 1086px)').matches) {
            return;
        }

        fallback.querySelectorAll('details[data-initially-open]').forEach(function (group) {
            group.open = true;
        });
    }(document.currentScript));
</script>
