const toId = (value) => String(value);

export function normalizeSelected(rawSelected = {}) {
    return Object.entries(rawSelected).reduce((selected, [propertyId, values]) => {
        if (!Array.isArray(values)) {
            return selected;
        }

        selected[toId(propertyId)] = [...new Set(values.map(toId))];

        return selected;
    }, {});
}

export function normalizePriceSelection(rawSelected = {}) {
    const normalizeValue = (value) => {
        if (value === null || value === undefined || value === '') {
            return null;
        }

        const normalized = Number(value);

        return Number.isFinite(normalized) ? Math.round(normalized) : null;
    };

    return {
        min: normalizeValue(rawSelected.min),
        max: normalizeValue(rawSelected.max),
    };
}

export function selectedFilterCount(selected = {}, selectedPrice = {}) {
    const propertyCount = Object.values(normalizeSelected(selected))
        .reduce((count, values) => count + values.length, 0);
    const price = normalizePriceSelection(selectedPrice);

    return propertyCount + (price.min !== null || price.max !== null ? 1 : 0);
}

export function toggleSelectedValue(selectedValues, valueId, checked) {
    const normalizedValues = [...new Set(selectedValues.map(toId))];
    const normalizedValueId = toId(valueId);

    if (checked) {
        return normalizedValues.includes(normalizedValueId)
            ? normalizedValues
            : [...normalizedValues, normalizedValueId];
    }

    return normalizedValues.filter((id) => id !== normalizedValueId);
}

export function buildFilterSearchParams(selected = {}, selectedPrice = {}) {
    const params = new URLSearchParams();

    Object.entries(normalizeSelected(selected)).forEach(([propertyId, values]) => {
        values.forEach((valueId) => {
            params.append(`properties[${propertyId}][]`, valueId);
        });
    });

    const price = normalizePriceSelection(selectedPrice);

    if (price.min !== null) {
        params.set('price_min', String(price.min));
    }

    if (price.max !== null) {
        params.set('price_max', String(price.max));
    }

    return params;
}

export function filterSelectionSignature(selected = {}, selectedPrice = {}) {
    const properties = Object.entries(normalizeSelected(selected))
        .sort(([firstId], [secondId]) => firstId.localeCompare(secondId, undefined, {numeric: true}))
        .map(([propertyId, values]) => [propertyId, [...values].sort()]);
    const price = normalizePriceSelection(selectedPrice);

    return JSON.stringify({
        properties,
        price: [price.min, price.max],
    });
}

export function productCountLabel(count) {
    const absoluteCount = Math.abs(Number(count));
    const lastTwoDigits = absoluteCount % 100;
    const lastDigit = absoluteCount % 10;

    if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
        return 'товаров';
    }

    if (lastDigit === 1) {
        return 'товар';
    }

    if (lastDigit >= 2 && lastDigit <= 4) {
        return 'товара';
    }

    return 'товаров';
}
