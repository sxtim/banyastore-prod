<template>
    <div class="catalog-filter-shell" data-testid="catalog-filters">
        <div class="catalog-filter-desktop">
            <CatalogFilterPanelComponent
                id-prefix="desktop"
                :properties="desktopProperties"
                :selected="selected"
                :selected-price="selectedPrice"
                :price-bounds="priceBounds"
                :result-count="resultCount"
                :is-loading="isDesktopLoading || isResultsLoading"
                :featured-property-ids="featuredPropertyIds"
                :brand-property-id="brandPropertyId"
                :always-open-property-ids="alwaysOpenPropertyIds"
                :option-preview-limit="optionPreviewLimit"
                @update:selected-values="setSelectedValues('desktop', $event.propertyId, $event.values)"
                @update:selected-price="setSelectedPrice('desktop', $event)"
                @clear-price="setSelectedPrice('desktop', emptyPrice())"
                @reset="resetFilters('desktop')"
                @request-context-submit="setDesktopSubmitAnchor"
            />
        </div>

        <div class="catalog-filter-mobile">
            <div class="catalog-filter-mobile__toolbar">
                <button
                    class="catalog-filter-mobile__open"
                    type="button"
                    data-testid="catalog-filter-mobile-open"
                    @click="openMobileFilters"
                >
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" aria-hidden="true">
                        <path d="M3 4H15M5 9H13M7 14H11" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                    <span>Фильтры</span>
                    <span v-if="activeFilterCount" class="catalog-filter-mobile__badge">{{ activeFilterCount }}</span>
                </button>
                <span class="catalog-filter-mobile__result">{{ resultSummary }}</span>
            </div>

        </div>

        <DialogRoot :open="mobileOpen" @update:open="onMobileOpenChange">
            <DialogPortal>
                <DialogOverlay class="catalog-filter-dialog__overlay" />
                <DialogContent
                    class="catalog-filter-dialog__content"
                    aria-describedby="catalog-filter-dialog-description"
                    data-testid="catalog-filter-mobile-dialog"
                >
                    <header class="catalog-filter-dialog__header">
                        <div>
                            <DialogTitle class="catalog-filter-dialog__title">
                                Фильтры
                                <span v-if="mobileActiveFilterCount">({{ mobileActiveFilterCount }})</span>
                            </DialogTitle>
                            <DialogDescription id="catalog-filter-dialog-description" class="catalog-filter-dialog__description">
                                {{ mobileResultSummary }}
                            </DialogDescription>
                        </div>
                        <button
                            v-if="mobileActiveFilterCount"
                            class="catalog-filter-dialog__reset"
                            type="button"
                            data-testid="catalog-filter-mobile-reset"
                            @click="resetFilters('mobile')"
                        >
                            Сбросить
                        </button>
                        <DialogClose class="catalog-filter-dialog__close" aria-label="Закрыть фильтры">
                            ×
                        </DialogClose>
                    </header>

                    <CatalogFilterPanelComponent
                        id-prefix="mobile"
                        submit-type="button"
                        :show-header="false"
                        :properties="mobileProperties"
                        :selected="mobileSelected"
                        :selected-price="mobileSelectedPrice"
                        :price-bounds="priceBounds"
                        :result-count="mobileResultCount"
                        :is-loading="isMobileLoading || isResultsLoading"
                        :featured-property-ids="featuredPropertyIds"
                        :brand-property-id="brandPropertyId"
                        :always-open-property-ids="alwaysOpenPropertyIds"
                        :option-preview-limit="optionPreviewLimit"
                        @update:selected-values="setSelectedValues('mobile', $event.propertyId, $event.values)"
                        @update:selected-price="setSelectedPrice('mobile', $event)"
                        @clear-price="setSelectedPrice('mobile', emptyPrice())"
                        @reset="resetFilters('mobile')"
                        @submit="applyMobileFilters"
                    />
                </DialogContent>
            </DialogPortal>
        </DialogRoot>

        <Teleport to="body">
            <Transition name="catalog-filter-context-submit">
                <button
                    v-if="showDesktopContextSubmit"
                    ref="desktopContextSubmit"
                    class="catalog-filter-context-submit"
                    type="submit"
                    form="catalog-filter-form"
                    :class="[
                        `catalog-filter-context-submit--${desktopSubmitPosition.side}`,
                        {'catalog-filter-context-submit--loading': desktopContextLoading},
                    ]"
                    :style="{
                        left: `${desktopSubmitPosition.left}px`,
                        top: `${desktopSubmitPosition.top}px`,
                    }"
                    :disabled="resultCount === 0 || desktopContextLoading"
                    :aria-busy="desktopContextLoading ? 'true' : 'false'"
                    :aria-label="desktopContextLoading ? 'Считаем количество товаров' : desktopContextSubmitLabel"
                    data-testid="catalog-filter-context-submit"
                >
                    <span
                        v-if="desktopContextLoading"
                        class="catalog-filter-context-submit__spinner"
                        aria-hidden="true"
                        data-testid="catalog-filter-context-spinner"
                    />
                    <span v-else>{{ desktopContextSubmitLabel }}</span>
                </button>
            </Transition>
        </Teleport>

        <template v-for="property in properties" :key="`selected-${property.id}`">
            <input
                v-for="valueId in selectedValues('desktop', property.id)"
                :key="`${property.id}-${valueId}`"
                type="hidden"
                :name="`properties[${property.id}][]`"
                :value="valueId"
                data-testid="catalog-filter-selected-input"
            >
        </template>
        <input v-if="selectedPrice.min !== null" type="hidden" name="price_min" :value="selectedPrice.min">
        <input v-if="selectedPrice.max !== null" type="hidden" name="price_max" :value="selectedPrice.max">
    </div>
</template>

<script>
import {
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogOverlay,
    DialogPortal,
    DialogRoot,
    DialogTitle,
} from 'reka-ui';
import CatalogFilterPanelComponent from './CatalogFilterPanelComponent.vue';
import {
    buildFilterSearchParams,
    filterSelectionSignature,
    normalizePriceSelection,
    normalizeSelected,
    productCountLabel,
    selectedFilterCount,
} from './catalog-filter-state.mjs';

const cloneSelected = (selected) => normalizeSelected(JSON.parse(JSON.stringify(selected)));

export default {
    name: 'CatalogFiltersComponent',
    components: {
        CatalogFilterPanelComponent,
        DialogClose,
        DialogContent,
        DialogDescription,
        DialogOverlay,
        DialogPortal,
        DialogRoot,
        DialogTitle,
    },
    props: {
        properties: {type: Array, required: true},
        initialSelected: {type: Object, default: () => ({})},
        initialFacets: {type: Object, default: () => ({})},
        initialCount: {type: Number, required: true},
        initialSelectedPrice: {type: Object, default: () => ({})},
        initialPriceBounds: {type: Object, required: true},
        featuredPropertyIds: {type: Array, default: () => []},
        brandPropertyId: {type: [String, Number], default: null},
        alwaysOpenPropertyIds: {type: Array, default: () => []},
        optionPreviewLimit: {type: Number, default: 6},
        facetsUrl: {type: String, required: true},
        resultsUrl: {type: String, required: true},
    },
    data() {
        const selected = normalizeSelected(this.initialSelected);
        const selectedPrice = normalizePriceSelection(this.initialSelectedPrice);

        return {
            selected,
            selectedPrice,
            facets: this.initialFacets,
            resultCount: this.initialCount,
            priceBounds: {
                min: Number(this.initialPriceBounds.min ?? 0),
                max: Number(this.initialPriceBounds.max ?? 0),
            },
            mobileOpen: false,
            mobileSelected: cloneSelected(selected),
            mobileSelectedPrice: {...selectedPrice},
            mobileFacets: this.initialFacets,
            mobileResultCount: this.initialCount,
            facetTimer: null,
            facetController: null,
            facetTarget: null,
            resultsController: null,
            isDesktopLoading: false,
            isMobileLoading: false,
            isResultsLoading: false,
            appliedFilterSignature: filterSelectionSignature(selected, selectedPrice),
            desktopSubmitAnchor: null,
            desktopSubmitPosition: null,
        };
    },
    computed: {
        activeFilterCount() {
            return selectedFilterCount(this.selected, this.selectedPrice);
        },
        mobileActiveFilterCount() {
            return selectedFilterCount(this.mobileSelected, this.mobileSelectedPrice);
        },
        resultSummary() {
            return `${this.resultCount} ${productCountLabel(this.resultCount)}`;
        },
        mobileResultSummary() {
            return `${this.mobileResultCount} ${productCountLabel(this.mobileResultCount)}`;
        },
        desktopProperties() {
            return this.decorateProperties(this.facets);
        },
        mobileProperties() {
            return this.decorateProperties(this.mobileFacets);
        },
        desktopFiltersDirty() {
            return filterSelectionSignature(this.selected, this.selectedPrice) !== this.appliedFilterSignature;
        },
        desktopContextLoading() {
            return this.isDesktopLoading || this.isResultsLoading;
        },
        desktopContextSubmitLabel() {
            if (this.resultCount === 0) {
                return 'НЕТ ТОВАРОВ';
            }

            return `ПОКАЗАТЬ ${this.resultSummary.toUpperCase()}`;
        },
        showDesktopContextSubmit() {
            return this.desktopFiltersDirty && this.desktopSubmitPosition !== null;
        },
    },
    mounted() {
        this.form = this.$el.closest('form');
        this.form?.addEventListener('submit', this.onSubmit);
        document.addEventListener('click', this.onCatalogLinkClick);
        window.addEventListener('popstate', this.onPopState);
        this.desktopFilterPositionHandler = () => {
            if (this.desktopFilterPositionFrame) {
                return;
            }

            this.desktopFilterPositionFrame = requestAnimationFrame(() => {
                this.desktopFilterPositionFrame = null;
                this.updateDesktopFilterPosition();
                this.updateDesktopContextSubmitPosition();
            });
        };
        this.desktopFilterResizeObserver = window.ResizeObserver
            ? new ResizeObserver(this.desktopFilterPositionHandler)
            : null;
        this.desktopFilterResizeObserver?.observe(this.$el.closest('.catalog__layout'));
        this.desktopFilterResizeObserver?.observe(this.$el.querySelector('[data-panel="desktop"].catalog-filter-panel'));
        window.addEventListener('resize', this.desktopFilterPositionHandler);
        window.addEventListener('scroll', this.desktopFilterPositionHandler, {passive: true});
        this.$nextTick(() => this.updateDesktopFilterPosition());
    },
    beforeUnmount() {
        window.removeEventListener('resize', this.desktopFilterPositionHandler);
        window.removeEventListener('scroll', this.desktopFilterPositionHandler);
        if (this.desktopFilterPositionFrame) {
            cancelAnimationFrame(this.desktopFilterPositionFrame);
        }
        this.desktopFilterResizeObserver?.disconnect();
        this.resetDesktopFilterPosition();
        this.clearDesktopSubmitAnchor();
        this.form?.removeEventListener('submit', this.onSubmit);
        document.removeEventListener('click', this.onCatalogLinkClick);
        window.removeEventListener('popstate', this.onPopState);
        window.clearTimeout(this.facetTimer);
        this.facetController?.abort();
        this.resultsController?.abort();
    },
    methods: {
        setDesktopSubmitAnchor(anchor) {
            if (!(anchor instanceof HTMLElement) || window.innerWidth < 1086) {
                return;
            }

            this.desktopSubmitAnchor = anchor;
            this.$nextTick(() => this.updateDesktopContextSubmitPosition());
        },
        updateDesktopContextSubmitPosition() {
            const anchor = this.desktopSubmitAnchor;

            if (!this.desktopFiltersDirty || !anchor?.isConnected || window.innerWidth < 1086) {
                this.desktopSubmitPosition = null;
                return;
            }

            const rect = anchor.getBoundingClientRect();
            if (rect.width === 0 || rect.height === 0 || rect.bottom <= 0 || rect.top >= window.innerHeight) {
                this.desktopSubmitPosition = null;
                return;
            }

            const sideGap = 14;
            const viewportGap = 12;
            const buttonWidth = this.$refs.desktopContextSubmit?.offsetWidth ?? 210;
            const buttonHalfHeight = (this.$refs.desktopContextSubmit?.offsetHeight ?? 48) / 2;
            const canFitRight = rect.right + sideGap + buttonWidth + viewportGap <= window.innerWidth;
            const side = canFitRight ? 'right' : 'left';
            const left = canFitRight
                ? rect.right + sideGap
                : Math.max(viewportGap, rect.left - sideGap - buttonWidth);
            const top = Math.min(
                window.innerHeight - viewportGap - buttonHalfHeight,
                Math.max(viewportGap + buttonHalfHeight, rect.top + rect.height / 2),
            );

            this.desktopSubmitPosition = {left, top, side};
        },
        clearDesktopSubmitAnchor() {
            this.desktopSubmitAnchor = null;
            this.desktopSubmitPosition = null;
        },
        updateDesktopFilterPosition() {
            const panel = this.$el.querySelector('[data-panel="desktop"].catalog-filter-panel');
            const track = this.$el.querySelector('.catalog-filter-desktop');
            const layout = this.$el.closest('.catalog__layout');

            if (!panel || !track || !layout || window.innerWidth < 1086) {
                this.resetDesktopFilterPosition();
                return;
            }

            const topGap = 20;
            const bottomGap = 30;
            const layoutRect = layout.getBoundingClientRect();
            const trackRect = track.getBoundingClientRect();
            const panelHeight = panel.offsetHeight;
            const layoutTop = layoutRect.top + window.scrollY;
            const layoutBottom = layoutRect.bottom + window.scrollY;
            const panelBottom = layoutTop + panelHeight;
            const viewportTop = window.scrollY + topGap;
            const viewportBottom = window.scrollY + window.innerHeight - bottomGap;
            const fitsViewport = panelHeight <= window.innerHeight - topGap - bottomGap;
            const fixedPanelBottom = fitsViewport
                ? viewportTop + panelHeight
                : viewportBottom;
            const shouldAnchorToLayout = fixedPanelBottom >= layoutBottom;
            const shouldFix = fitsViewport
                ? viewportTop >= layoutTop && !shouldAnchorToLayout
                : viewportBottom >= panelBottom && !shouldAnchorToLayout;

            if (track.style.height !== `${Math.ceil(layoutRect.height)}px`) {
                track.style.height = `${Math.ceil(layoutRect.height)}px`;
            }

            if (shouldAnchorToLayout) {
                panel.style.position = 'absolute';
                panel.style.top = 'auto';
                panel.style.right = '0';
                panel.style.bottom = '0';
                panel.style.left = '0';
                panel.style.width = '100%';
                panel.style.zIndex = '1';
                return;
            }

            if (shouldFix) {
                panel.style.position = 'fixed';
                panel.style.top = fitsViewport ? `${topGap}px` : 'auto';
                panel.style.right = 'auto';
                panel.style.bottom = fitsViewport ? 'auto' : `${bottomGap}px`;
                panel.style.left = `${trackRect.left}px`;
                panel.style.width = `${trackRect.width}px`;
                panel.style.zIndex = '20';
                return;
            }

            this.resetDesktopFilterPosition(false);
        },
        resetDesktopFilterPosition(resetTrack = true) {
            const panel = this.$el?.querySelector('[data-panel="desktop"].catalog-filter-panel');
            const track = this.$el?.querySelector('.catalog-filter-desktop');

            if (panel) {
                panel.style.removeProperty('position');
                panel.style.removeProperty('top');
                panel.style.removeProperty('right');
                panel.style.removeProperty('bottom');
                panel.style.removeProperty('left');
                panel.style.removeProperty('width');
                panel.style.removeProperty('z-index');
            }

            if (resetTrack && track) {
                track.style.removeProperty('height');
            }
        },
        emptyPrice() {
            return {min: null, max: null};
        },
        selectedValues(target, propertyId) {
            const selected = target === 'mobile' ? this.mobileSelected : this.selected;
            return selected[String(propertyId)] ?? [];
        },
        setSelectedValues(target, propertyId, values) {
            const key = String(propertyId);
            const selected = target === 'mobile' ? this.mobileSelected : this.selected;
            selected[key] = values.map(String);
            if (selected[key].length === 0) {
                delete selected[key];
            }
            this.scheduleFacetsUpdate(target);
        },
        setSelectedPrice(target, value) {
            const normalized = normalizePriceSelection(value);
            if (target === 'mobile') {
                this.mobileSelectedPrice = normalized;
            } else {
                this.selectedPrice = normalized;
            }
            this.scheduleFacetsUpdate(target);
        },
        resetFilters(target) {
            if (target === 'mobile') {
                this.mobileSelected = {};
                this.mobileSelectedPrice = this.emptyPrice();
            } else {
                this.selected = {};
                this.selectedPrice = this.emptyPrice();
            }
            this.scheduleFacetsUpdate(target);
        },
        decorateProperties(facets) {
            return this.properties.map((property) => ({
                ...property,
                facetCounts: facets?.[String(property.id)] ?? {},
            }));
        },
        scheduleFacetsUpdate(target) {
            window.clearTimeout(this.facetTimer);
            this.facetTarget = target;
            this.setTargetLoading(target, true);
            this.facetTimer = window.setTimeout(() => this.fetchFacets(target), 200);
        },
        async fetchFacets(target) {
            this.facetController?.abort();
            const controller = new AbortController();
            const selected = target === 'mobile' ? this.mobileSelected : this.selected;
            const selectedPrice = target === 'mobile' ? this.mobileSelectedPrice : this.selectedPrice;
            this.facetController = controller;
            this.setTargetLoading(target, true);

            const url = new URL(this.facetsUrl, window.location.origin);
            url.search = buildFilterSearchParams(selected, selectedPrice).toString();

            try {
                const response = await fetch(url, {
                    headers: {'Accept': 'application/json'},
                    signal: controller.signal,
                });

                if (!response.ok) {
                    throw new Error(`Facet request failed: ${response.status}`);
                }

                const data = await response.json();
                if (target === 'mobile') {
                    this.mobileFacets = data.facets;
                    this.mobileResultCount = Number(data.result_count);
                } else {
                    this.facets = data.facets;
                    this.resultCount = Number(data.result_count);
                    this.$nextTick(() => this.updateDesktopContextSubmitPosition());
                }
            } catch (error) {
                if (error.name !== 'AbortError') {
                    this.announceRequestFailure();
                }
            } finally {
                if (this.facetController === controller) {
                    this.setTargetLoading(target, false);
                }
            }
        },
        setTargetLoading(target, loading) {
            if (target === 'mobile') {
                this.isMobileLoading = loading;
            } else {
                this.isDesktopLoading = loading;
            }
        },
        openMobileFilters() {
            this.mobileSelected = cloneSelected(this.selected);
            this.mobileSelectedPrice = {...this.selectedPrice};
            this.mobileFacets = this.facets;
            this.mobileResultCount = this.resultCount;
            this.mobileOpen = true;
        },
        onMobileOpenChange(open) {
            this.mobileOpen = open;
            if (!open && this.facetTarget === 'mobile') {
                window.clearTimeout(this.facetTimer);
                this.facetController?.abort();
                this.isMobileLoading = false;
            }
        },
        async applyMobileFilters() {
            window.clearTimeout(this.facetTimer);
            if (this.isMobileLoading) {
                await this.fetchFacets('mobile');
            }

            this.selected = cloneSelected(this.mobileSelected);
            this.selectedPrice = {...this.mobileSelectedPrice};
            this.facets = this.mobileFacets;
            this.resultCount = this.mobileResultCount;

            const loaded = await this.loadResults(this.buildResultsUrl(this.selected, this.selectedPrice));
            if (loaded) {
                this.mobileOpen = false;
            }
        },
        onSubmit(event) {
            event.preventDefault();
            this.loadResults(this.buildResultsUrl(this.selected, this.selectedPrice));
        },
        buildResultsUrl(selected, selectedPrice) {
            const url = new URL(this.resultsUrl, window.location.origin);
            const currentUrl = new URL(window.location.href);
            url.search = buildFilterSearchParams(selected, selectedPrice).toString();
            ['sort', 'sortDesk'].forEach((key) => {
                if (currentUrl.searchParams.has(key)) {
                    url.searchParams.set(key, currentUrl.searchParams.get(key));
                }
            });
            return url.toString();
        },
        onCatalogLinkClick(event) {
            const link = event.target.closest('[data-catalog-pagination] a, [data-catalog-sort] a');

            if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
                return;
            }

            event.preventDefault();
            this.loadResults(link.href);
        },
        onPopState() {
            this.loadResults(window.location.href, false);
        },
        async loadResults(url, updateHistory = true) {
            window.clearTimeout(this.facetTimer);
            this.facetController?.abort();
            this.isDesktopLoading = false;
            this.isMobileLoading = false;
            this.resultsController?.abort();
            const controller = new AbortController();
            this.resultsController = controller;
            this.isResultsLoading = true;

            try {
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                });

                if (!response.ok) {
                    throw new Error(`Catalog request failed: ${response.status}`);
                }

                const data = await response.json();
                this.selected = normalizeSelected(data.selected);
                this.selectedPrice = normalizePriceSelection(data.selected_price);
                this.facets = data.facets;
                this.resultCount = Number(data.result_count);
                this.priceBounds = data.price_bounds ?? this.priceBounds;
                this.appliedFilterSignature = filterSelectionSignature(this.selected, this.selectedPrice);
                this.clearDesktopSubmitAnchor();
                window.dispatchEvent(new CustomEvent('catalog:replace-results', {
                    detail: {html: data.html},
                }));

                if (updateHistory) {
                    window.history.pushState({catalog: true}, '', url);
                }
                return true;
            } catch (error) {
                if (error.name !== 'AbortError') {
                    window.location.assign(url);
                }
                return false;
            } finally {
                if (this.resultsController === controller) {
                    this.isResultsLoading = false;
                }
            }
        },
        announceRequestFailure() {
            window.dispatchEvent(new CustomEvent('catalog:filter-error'));
        },
    },
};
</script>
