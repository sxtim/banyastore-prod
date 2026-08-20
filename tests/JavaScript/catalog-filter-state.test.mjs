import assert from 'node:assert/strict';
import test from 'node:test';
import {
    buildFilterSearchParams,
    filterSelectionSignature,
    normalizePriceSelection,
    normalizeSelected,
    productCountLabel,
    selectedFilterCount,
    toggleSelectedValue,
} from '../../resources/js/blocks/vue/Filters/catalog-filter-state.mjs';

test('normalizeSelected converts ids to unique strings and ignores invalid groups', () => {
    assert.deepEqual(normalizeSelected({3: [5, '7', 5], invalid: null}), {
        3: ['5', '7'],
    });
});

test('toggleSelectedValue adds a value once', () => {
    assert.deepEqual(toggleSelectedValue(['5'], 7, true), ['5', '7']);
    assert.deepEqual(toggleSelectedValue(['5'], 5, true), ['5']);
});

test('toggleSelectedValue removes only the requested value', () => {
    assert.deepEqual(toggleSelectedValue(['5', '7'], 5, false), ['7']);
});

test('buildFilterSearchParams creates Laravel array parameters for every selected value', () => {
    const params = buildFilterSearchParams({3: [5, 7], 4: [8]});

    assert.deepEqual(params.getAll('properties[3][]'), ['5', '7']);
    assert.deepEqual(params.getAll('properties[4][]'), ['8']);
});

test('buildFilterSearchParams adds only active price limits', () => {
    const params = buildFilterSearchParams({3: [5]}, {min: 75000, max: null});

    assert.equal(params.get('price_min'), '75000');
    assert.equal(params.has('price_max'), false);
});

test('filterSelectionSignature ignores property and value order', () => {
    const first = filterSelectionSignature({3: ['7', '5'], 1: ['2']}, {max: 300000});
    const second = filterSelectionSignature({1: [2], 3: [5, 7]}, {min: null, max: '300000'});

    assert.equal(first, second);
});

test('normalizePriceSelection keeps nullable numeric limits', () => {
    assert.deepEqual(normalizePriceSelection({min: '75000', max: ''}), {
        min: 75000,
        max: null,
    });
});

test('selectedFilterCount includes values and one active price range', () => {
    assert.equal(selectedFilterCount({3: [5, 7], 4: [8]}, {min: 75000}), 4);
    assert.equal(selectedFilterCount({}, {}), 0);
});

test('productCountLabel uses Russian plural forms', () => {
    assert.equal(productCountLabel(1), 'товар');
    assert.equal(productCountLabel(2), 'товара');
    assert.equal(productCountLabel(5), 'товаров');
    assert.equal(productCountLabel(11), 'товаров');
    assert.equal(productCountLabel(21), 'товар');
});
