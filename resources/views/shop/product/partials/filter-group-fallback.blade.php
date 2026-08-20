@php
    $selectedValues = $selectedProperties->get((string) $property['id'], []);
    $values = collect($property['values']);
@endphp

<details
    class="catalog-filter-group {{ ($alwaysOpen ?? false) ? 'catalog-filter-group--always-open' : '' }}"
    data-property-id="{{ $property['id'] }}"
    @if($alwaysOpen ?? false) data-initially-open @endif
>
    <summary class="catalog-filter-group__trigger">
        <span>
            <span class="catalog-filter-group__name">{{ $property['name'] }}</span>
        </span>
        <span class="catalog-filter-group__chevron" aria-hidden="true">⌄</span>
    </summary>
    <div class="catalog-filter-group__content">
        <ul class="catalog-filter-group__options">
            @foreach($values as $value)
                @php
                    $selected = in_array((string) $value['id'], $selectedValues, true);
                    $disabled = ! $selected
                        && (int) data_get($facets, $property['id'].'.'.$value['id'], 0) === 0;
                @endphp
                <li>
                    <label class="catalog-filter-option {{ $disabled ? 'catalog-filter-option--disabled' : '' }}">
                        <input
                            type="checkbox"
                            name="properties[{{ $property['id'] }}][]"
                            value="{{ $value['id'] }}"
                            @checked($selected)
                            @disabled($disabled)
                        >
                        <span class="catalog-filter-option__label">{{ $value['name'] }}</span>
                        <span class="catalog-filter-option__count">
                            {{ (int) data_get($facets, $property['id'].'.'.$value['id'], 0) }}
                        </span>
                    </label>
                </li>
            @endforeach
        </ul>
    </div>
</details>
