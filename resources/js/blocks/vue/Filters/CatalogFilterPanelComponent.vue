<template>
    <div class="catalog-filter-panel" :data-panel="idPrefix">
        <header v-if="showHeader" class="catalog-filter-panel__header">
            <div>
                <h2 class="catalog-filter-panel__title">Фильтры</h2>
                <span class="catalog-filter-panel__result-count">{{ resultSummary }}</span>
            </div>
            <button
                v-if="activeFilterCount"
                class="catalog-filter-panel__reset"
                type="button"
                data-testid="catalog-filter-reset"
                @click="onReset"
            >
                Сбросить
            </button>
        </header>

        <div class="catalog-filter-panel__scroll">
            <AccordionRoot
                v-model="openGroups"
                class="catalog-filter-accordion"
                type="multiple"
                collapsible
            >
                <template v-for="item in filterItems" :key="item.key">
                    <AccordionItem
                        v-if="item.type === 'price'"
                        class="catalog-filter-group catalog-filter-group--always-open"
                        value="price"
                        data-filter-key="price"
                    >
                        <AccordionHeader class="catalog-filter-group__heading">
                            <AccordionTrigger
                                class="catalog-filter-group__trigger"
                                data-testid="catalog-filter-trigger"
                            >
                                <span>
                                    <span class="catalog-filter-group__name">Цена</span>
                                </span>
                                <span class="catalog-filter-group__chevron" aria-hidden="true">⌄</span>
                            </AccordionTrigger>
                        </AccordionHeader>
                        <AccordionContent class="catalog-filter-group__content" data-testid="catalog-filter-content">
                            <CatalogPriceFilterComponent
                                :selected-price="selectedPrice"
                                :price-bounds="priceBounds"
                                @update:selected-price="onPriceChange"
                            />
                            <button
                                v-if="priceIsSelected"
                                class="catalog-filter-group__clear"
                                type="button"
                                @click="onClearPrice"
                            >
                                Сбросить цену
                            </button>
                        </AccordionContent>
                    </AccordionItem>

                    <AccordionItem
                        v-else
                        class="catalog-filter-group"
                        :class="{'catalog-filter-group--always-open': isAlwaysOpen(item.property.id)}"
                        :value="String(item.property.id)"
                        :data-property-id="item.property.id"
                        data-testid="catalog-filter-dropdown"
                    >
                        <AccordionHeader class="catalog-filter-group__heading">
                            <AccordionTrigger
                                class="catalog-filter-group__trigger"
                                data-testid="catalog-filter-trigger"
                            >
                                <span>
                                    <span class="catalog-filter-group__name">{{ item.property.name }}</span>
                                </span>
                                <span class="catalog-filter-group__chevron" aria-hidden="true">⌄</span>
                            </AccordionTrigger>
                        </AccordionHeader>
                        <AccordionContent class="catalog-filter-group__content" data-testid="catalog-filter-content">
                            <ul class="catalog-filter-group__options">
                                <li v-for="value in visibleOptions(item.property)" :key="value.id">
                                    <label
                                        class="catalog-filter-option"
                                        :class="{'catalog-filter-option--disabled': isOptionDisabled(item.property, value.id)}"
                                        :for="inputId(item.property.id, value.id)"
                                    >
                                        <input
                                            :id="inputId(item.property.id, value.id)"
                                            class="catalog-filter-option__native"
                                            type="checkbox"
                                            :value="value.id"
                                            :checked="isSelected(item.property.id, value.id)"
                                            :disabled="isOptionDisabled(item.property, value.id)"
                                            :data-filter-name="`properties[${item.property.id}][]`"
                                            data-testid="catalog-filter-option"
                                            @change="onChange(item.property.id, value.id, $event)"
                                        >
                                        <span class="catalog-filter-option__checkbox" aria-hidden="true">✓</span>
                                        <span class="catalog-filter-option__label">{{ value.name }}</span>
                                        <span class="catalog-filter-option__count" aria-hidden="true">
                                            {{ facetCount(item.property, value.id) }}
                                        </span>
                                    </label>
                                </li>
                            </ul>

                            <p v-if="visibleOptions(item.property).length === 0" class="catalog-filter-group__empty">
                                Ничего не найдено
                            </p>

                            <button
                                v-if="canToggleOptions(item.property)"
                                class="catalog-filter-group__more"
                                type="button"
                                @click="toggleOptions(item.property.id)"
                            >
                                {{ expandedOptions[String(item.property.id)] ? 'Скрыть' : `Показать ещё ${hiddenOptionsCount(item.property)}` }}
                            </button>
                        </AccordionContent>
                    </AccordionItem>
                </template>
            </AccordionRoot>

            <button
                v-if="additionalProperties.length"
                class="catalog-filter-panel__additional"
                type="button"
                :aria-expanded="showAdditional ? 'true' : 'false'"
                data-testid="catalog-filter-additional"
                @click="showAdditional = !showAdditional"
            >
                <span>{{ showAdditional ? 'Скрыть дополнительные' : `Все характеристики (${additionalProperties.length})` }}</span>
                <span class="catalog-filter-group__chevron" aria-hidden="true">⌄</span>
            </button>
        </div>

        <footer class="catalog-filter-panel__footer">
            <button
                class="catalog__filter-submit btn btn-medium"
                :type="submitType"
                :disabled="resultCount === 0 || isLoading"
                :aria-busy="isLoading ? 'true' : 'false'"
                data-testid="catalog-filter-submit"
                @click="submitType === 'button' && $emit('submit')"
            >
                {{ submitLabel }}
            </button>
            <span class="catalog-filter-status" aria-live="polite" aria-atomic="true">
                {{ isLoading ? 'Обновляем доступные параметры' : resultSummary }}
            </span>
        </footer>
    </div>
</template>

<script>
import {
    AccordionContent,
    AccordionHeader,
    AccordionItem,
    AccordionRoot,
    AccordionTrigger,
} from 'reka-ui';
import CatalogPriceFilterComponent from './CatalogPriceFilterComponent.vue';
import {
    productCountLabel,
    selectedFilterCount,
    toggleSelectedValue,
} from './catalog-filter-state.mjs';

export default {
    name: 'CatalogFilterPanelComponent',
    components: {
        AccordionContent,
        AccordionHeader,
        AccordionItem,
        AccordionRoot,
        AccordionTrigger,
        CatalogPriceFilterComponent,
    },
    props: {
        properties: {type: Array, required: true},
        selected: {type: Object, required: true},
        selectedPrice: {type: Object, required: true},
        priceBounds: {type: Object, required: true},
        resultCount: {type: Number, required: true},
        isLoading: {type: Boolean, default: false},
        featuredPropertyIds: {type: Array, default: () => []},
        brandPropertyId: {type: [String, Number], default: null},
        alwaysOpenPropertyIds: {type: Array, default: () => []},
        optionPreviewLimit: {type: Number, default: 6},
        idPrefix: {type: String, required: true},
        submitType: {type: String, default: 'submit'},
        showHeader: {type: Boolean, default: true},
    },
    emits: [
        'clear-price',
        'reset',
        'request-context-submit',
        'submit',
        'update:selected-price',
        'update:selected-values',
    ],
    data() {
        return {
            expandedOptions: {},
            openGroups: [
                'price',
                ...this.alwaysOpenPropertyIds.map(String),
            ],
            showAdditional: false,
        };
    },
    computed: {
        activeFilterCount() {
            return selectedFilterCount(this.selected, this.selectedPrice);
        },
        primaryProperties() {
            const configuredIds = this.featuredPropertyIds.map(String);
            const configured = configuredIds
                .map((id) => this.properties.find((property) => String(property.id) === id))
                .filter(Boolean);

            return configured.length ? configured : this.properties.slice(0, 6);
        },
        additionalProperties() {
            const primaryIds = new Set(this.primaryProperties.map(({id}) => String(id)));

            return this.properties.filter(({id}) => !primaryIds.has(String(id)));
        },
        visibleProperties() {
            if (this.showAdditional) {
                return [...this.primaryProperties, ...this.additionalProperties];
            }

            const selectedAdditional = this.additionalProperties.filter(
                (property) => this.selectedValues(property.id).length > 0,
            );

            return [...this.primaryProperties, ...selectedAdditional];
        },
        filterItems() {
            const properties = [...this.visibleProperties];
            const brandIndex = properties.findIndex((property) => (
                String(property.id) === String(this.brandPropertyId)
            ));
            const items = [];

            if (brandIndex >= 0) {
                items.push({
                    key: `property-${properties[brandIndex].id}`,
                    type: 'property',
                    property: properties[brandIndex],
                });
                properties.splice(brandIndex, 1);
            }

            items.push({key: 'price', type: 'price'});
            properties.forEach((property) => {
                items.push({
                    key: `property-${property.id}`,
                    type: 'property',
                    property,
                });
            });

            return items;
        },
        priceIsSelected() {
            return this.selectedPrice.min !== null || this.selectedPrice.max !== null;
        },
        resultSummary() {
            return `${this.resultCount} ${productCountLabel(this.resultCount)}`;
        },
        submitLabel() {
            if (this.isLoading) {
                return 'ОБНОВЛЯЕМ…';
            }

            if (this.resultCount === 0) {
                return 'НЕТ ТОВАРОВ';
            }

            return `ПОКАЗАТЬ ${this.resultSummary.toUpperCase()}`;
        },
    },
    methods: {
        selectedValues(propertyId) {
            return this.selected[String(propertyId)] ?? [];
        },
        isSelected(propertyId, valueId) {
            return this.selectedValues(propertyId).map(String).includes(String(valueId));
        },
        inputId(propertyId, valueId) {
            return `${this.idPrefix}-filter-${propertyId}-${valueId}`;
        },
        facetCount(property, valueId) {
            return Number(property.facetCounts?.[String(valueId)] ?? 0);
        },
        isOptionDisabled(property, valueId) {
            return !this.isSelected(property.id, valueId) && this.facetCount(property, valueId) === 0;
        },
        visibleOptions(property) {
            if (this.expandedOptions[String(property.id)]) {
                return property.values;
            }

            return property.values.filter((value, index) => (
                index < this.optionPreviewLimit || this.isSelected(property.id, value.id)
            ));
        },
        canToggleOptions(property) {
            return property.values.length > this.optionPreviewLimit;
        },
        hiddenOptionsCount(property) {
            return Math.max(0, property.values.length - this.visibleOptions(property).length);
        },
        isAlwaysOpen(propertyId) {
            return this.alwaysOpenPropertyIds.map(String).includes(String(propertyId));
        },
        toggleOptions(propertyId) {
            this.expandedOptions[String(propertyId)] = !this.expandedOptions[String(propertyId)];
        },
        requestContextSubmit(anchor) {
            this.$emit('request-context-submit', anchor);
        },
        onPriceChange(value) {
            this.$emit('update:selected-price', value);
            this.requestContextSubmit(this.$el.querySelector('[data-filter-key="price"]'));
        },
        onClearPrice(event) {
            this.$emit('clear-price');
            this.requestContextSubmit(event.currentTarget.closest('[data-filter-key="price"]'));
        },
        onReset(event) {
            this.$emit('reset');
            this.requestContextSubmit(event.currentTarget);
        },
        onChange(propertyId, valueId, event) {
            this.$emit(
                'update:selected-values',
                {
                    propertyId,
                    values: toggleSelectedValue(this.selectedValues(propertyId), valueId, event.target.checked),
                },
            );
            this.requestContextSubmit(event.currentTarget.closest('.catalog-filter-option'));
        },
    },
};
</script>
