<template>
    <div class="catalog-price-filter" :class="{'catalog-price-filter--disabled': disabled}">
        <div class="catalog-price-filter__inputs">
            <label>
                <span>От</span>
                <input
                    type="number"
                    inputmode="numeric"
                    :min="bounds.min"
                    :max="displayValue[1]"
                    :value="displayValue[0]"
                    :disabled="disabled"
                    data-testid="catalog-price-min"
                    @change="onInput(0, $event.target.value)"
                >
            </label>
            <label>
                <span>До</span>
                <input
                    type="number"
                    inputmode="numeric"
                    :min="displayValue[0]"
                    :max="bounds.max"
                    :value="displayValue[1]"
                    :disabled="disabled"
                    data-testid="catalog-price-max"
                    @change="onInput(1, $event.target.value)"
                >
            </label>
        </div>

        <SliderRoot
            class="catalog-price-filter__slider"
            :model-value="displayValue"
            :min="bounds.min"
            :max="bounds.max"
            :step="100"
            :disabled="disabled"
            data-testid="catalog-price-slider"
            @update:model-value="onSliderUpdate"
        >
            <SliderTrack class="catalog-price-filter__track">
                <SliderRange class="catalog-price-filter__range" />
            </SliderTrack>
            <SliderThumb class="catalog-price-filter__thumb" aria-label="Минимальная цена" />
            <SliderThumb class="catalog-price-filter__thumb" aria-label="Максимальная цена" />
        </SliderRoot>
    </div>
</template>

<script>
import {SliderRange, SliderRoot, SliderThumb, SliderTrack} from 'reka-ui';

export default {
    name: 'CatalogPriceFilterComponent',
    components: {
        SliderRange,
        SliderRoot,
        SliderThumb,
        SliderTrack,
    },
    props: {
        selectedPrice: {
            type: Object,
            required: true,
        },
        priceBounds: {
            type: Object,
            required: true,
        },
    },
    emits: ['update:selected-price'],
    computed: {
        bounds() {
            const min = Number(this.priceBounds.min ?? 0);
            const max = Number(this.priceBounds.max ?? min);

            return {min, max};
        },
        disabled() {
            return this.bounds.max <= this.bounds.min;
        },
        displayValue() {
            const min = this.clamp(this.selectedPrice.min ?? this.bounds.min);
            const max = this.clamp(this.selectedPrice.max ?? this.bounds.max);

            return min <= max ? [min, max] : [max, min];
        },
    },
    methods: {
        clamp(value) {
            return Math.min(this.bounds.max, Math.max(this.bounds.min, Number(value)));
        },
        emitValue(values) {
            const min = this.clamp(values[0]);
            const max = this.clamp(values[1]);

            this.$emit('update:selected-price', {
                min: min <= this.bounds.min ? null : min,
                max: max >= this.bounds.max ? null : max,
            });
        },
        onSliderUpdate(values) {
            this.emitValue(values);
        },
        onInput(index, value) {
            const values = [...this.displayValue];
            values[index] = this.clamp(value);

            if (index === 0) {
                values[0] = Math.min(values[0], values[1]);
            } else {
                values[1] = Math.max(values[0], values[1]);
            }

            this.emitValue(values);
        },
    },
};
</script>
