const {expect, test} = require('@playwright/test');

const categoryUrl = '/category/peci-dlia-bani-i-sauny';

const desktopGroup = (page, propertyId) => (
    page.locator(`[data-panel="desktop"] [data-property-id="${propertyId}"]`)
);

const mobileGroup = (page, propertyId) => (
    page.locator(`[data-panel="mobile"] [data-property-id="${propertyId}"]`)
);

test.beforeEach(async ({page}) => {
    await page.route('https://mc.yandex.ru/**', (route) => route.abort());
    await page.goto(categoryUrl);
    await expect(page.getByTestId('catalog-filters')).toBeVisible();
});

test('renders a stretched sidebar with a document-flow filter and a three-column catalog', async ({page}) => {
    const sidebar = page.locator('.catalog__sidebar');
    const panel = page.locator('[data-panel="desktop"].catalog-filter-panel');
    const layout = page.locator('.catalog__layout');
    const grid = page.getByTestId('catalog-products-grid');
    const submit = page.getByTestId('catalog-filter-submit');
    const scroll = panel.locator('.catalog-filter-panel__scroll');
    await expect(page.locator('[data-panel="desktop"] [data-testid="catalog-filter-dropdown"]')).toHaveCount(6);
    await expect(page.locator('[data-panel="desktop"] [data-testid="catalog-filter-additional"]')).toContainText('17');
    await expect(panel.locator('input[type="search"]')).toHaveCount(0);
    await expect(submit).toContainText('ПОКАЗАТЬ 225 ТОВАРОВ');
    const sidebarBox = await sidebar.boundingBox();
    const columns = await grid.evaluate((element) => (
        getComputedStyle(element).gridTemplateColumns.split(' ').length
    ));
    const panelBox = await panel.boundingBox();
    const layoutBox = await layout.boundingBox();
    expect(sidebarBox.width).toBeGreaterThanOrEqual(275);
    expect(sidebarBox.width).toBeLessThanOrEqual(285);
    expect(columns).toBe(3);
    expect(await sidebar.evaluate((element) => getComputedStyle(element).position)).toBe('static');
    expect(Math.abs(sidebarBox.height - layoutBox.height)).toBeLessThan(2);
    expect(await panel.evaluate((element) => getComputedStyle(element).position)).toBe('static');
    expect(await panel.evaluate((element) => getComputedStyle(element).borderBottomWidth)).toBe('1px');
    expect(await panel.evaluate((element) => getComputedStyle(element).borderRadius)).toBe('5px');
    expect(panelBox.height).toBeGreaterThan(500);
    expect(await scroll.evaluate((element) => getComputedStyle(element).overflowY)).toBe('visible');
    expect(await scroll.evaluate((element) => getComputedStyle(element).scrollBehavior)).toBe('auto');
    expect(await scroll.evaluate((element) => element.scrollHeight)).toBe(await scroll.evaluate((element) => element.clientHeight));
    expect(await panel.locator('.catalog-filter-group__trigger').first().evaluate((element) => getComputedStyle(element).fontSize)).toBe('15px');
    expect(await panel.evaluate((element) => getComputedStyle(element).maxHeight)).toBe('none');
    await expect(submit).toBeVisible();
});

test('keeps the filter in the document flow alongside the catalog', async ({page}) => {
    const panel = page.locator('[data-panel="desktop"].catalog-filter-panel');
    const layout = page.locator('.catalog__layout');
    const initialPanelBox = await panel.boundingBox();
    const initialLayoutBox = await layout.boundingBox();

    expect(initialPanelBox.y).toBeGreaterThanOrEqual(initialLayoutBox.y);
    await page.evaluate(() => window.scrollTo({top: 400, behavior: 'instant'}));
    await expect.poll(async () => page.evaluate(() => window.scrollY)).toBe(400);
    const scrolledPanelBox = await panel.boundingBox();
    expect(Math.abs((initialPanelBox.y - scrolledPanelBox.y) - 400)).toBeLessThan(2);
});

test('pins the bottom of the filter while the catalog continues scrolling', async ({page}) => {
    const panel = page.locator('[data-panel="desktop"].catalog-filter-panel');
    const metrics = await page.evaluate(() => {
        const currentPanel = document.querySelector('[data-panel="desktop"].catalog-filter-panel');
        const layout = document.querySelector('.catalog__layout');
        const layoutRect = layout.getBoundingClientRect();

        return {
            panelHeight: currentPanel.offsetHeight,
            layoutTop: layoutRect.top + window.scrollY,
            layoutBottom: layoutRect.bottom + window.scrollY,
            viewportHeight: window.innerHeight,
        };
    });
    const fixedScroll = Math.ceil(
        metrics.layoutTop + metrics.panelHeight - metrics.viewportHeight + 30 + 40,
    );

    await page.evaluate((scrollY) => window.scrollTo({top: scrollY, behavior: 'instant'}), fixedScroll);
    await expect.poll(async () => panel.evaluate((element) => getComputedStyle(element).position)).toBe('fixed');

    const fixedBox = await panel.boundingBox();
    expect(Math.abs(metrics.viewportHeight - fixedBox.y - fixedBox.height - 30)).toBeLessThan(2);

    const absoluteScroll = Math.ceil(metrics.layoutBottom - metrics.viewportHeight + 40);
    await page.evaluate((scrollY) => window.scrollTo({top: scrollY, behavior: 'instant'}), absoluteScroll);
    await expect.poll(async () => panel.evaluate((element) => getComputedStyle(element).position)).toBe('absolute');

    const absoluteBottom = await page.evaluate(() => {
        const panelRect = document.querySelector('[data-panel="desktop"].catalog-filter-panel').getBoundingClientRect();
        const layoutRect = document.querySelector('.catalog__layout').getBoundingClientRect();

        return {
            panelBottom: panelRect.bottom + window.scrollY,
            layoutBottom: layoutRect.bottom + window.scrollY,
        };
    });
    expect(Math.abs(absoluteBottom.panelBottom - absoluteBottom.layoutBottom)).toBeLessThan(2);
});

test('pins a filter that fits the viewport to the top edge', async ({page}) => {
    await page.setViewportSize({width: 1440, height: 1400});

    const panel = page.locator('[data-panel="desktop"].catalog-filter-panel');
    const metrics = await page.evaluate(() => {
        const currentPanel = document.querySelector('[data-panel="desktop"].catalog-filter-panel');
        const layout = document.querySelector('.catalog__layout');
        const layoutRect = layout.getBoundingClientRect();

        return {
            panelHeight: currentPanel.offsetHeight,
            layoutTop: layoutRect.top + window.scrollY,
            layoutBottom: layoutRect.bottom + window.scrollY,
            viewportHeight: window.innerHeight,
        };
    });

    expect(metrics.panelHeight).toBeLessThanOrEqual(metrics.viewportHeight - 50);
    const fixedScroll = Math.ceil(metrics.layoutTop - 20 + 40);
    await page.evaluate((scrollY) => window.scrollTo({top: scrollY, behavior: 'instant'}), fixedScroll);
    await expect.poll(async () => panel.evaluate((element) => getComputedStyle(element).position)).toBe('fixed');

    const fixedBox = await panel.boundingBox();
    expect(Math.abs(fixedBox.y - 20)).toBeLessThan(2);

    const absoluteScroll = Math.ceil(metrics.layoutBottom - metrics.panelHeight - 20 + 40);
    await page.evaluate((scrollY) => window.scrollTo({top: scrollY, behavior: 'instant'}), absoluteScroll);
    await expect.poll(async () => panel.evaluate((element) => getComputedStyle(element).position)).toBe('absolute');

    const absoluteBottom = await page.evaluate(() => {
        const panelRect = document.querySelector('[data-panel="desktop"].catalog-filter-panel').getBoundingClientRect();
        const layoutRect = document.querySelector('.catalog__layout').getBoundingClientRect();

        return {
            panelBottom: panelRect.bottom + window.scrollY,
            layoutBottom: layoutRect.bottom + window.scrollY,
        };
    });
    expect(Math.abs(absoluteBottom.panelBottom - absoluteBottom.layoutBottom)).toBeLessThan(2);
});

test('switches from bottom to top pinning when closing a group makes the panel fit', async ({page}) => {
    await page.setViewportSize({width: 1440, height: 1200});
    const panel = page.locator('[data-panel="desktop"].catalog-filter-panel');
    const metrics = await page.evaluate(() => {
        const currentPanel = document.querySelector('[data-panel="desktop"].catalog-filter-panel');
        const layoutRect = document.querySelector('.catalog__layout').getBoundingClientRect();

        return {
            panelHeight: currentPanel.offsetHeight,
            layoutTop: layoutRect.top + window.scrollY,
            viewportHeight: window.innerHeight,
        };
    });

    expect(metrics.panelHeight).toBeGreaterThan(metrics.viewportHeight - 50);
    const fixedScroll = Math.ceil(
        metrics.layoutTop + metrics.panelHeight - metrics.viewportHeight + 30 + 40,
    );
    await page.evaluate((scrollY) => window.scrollTo({top: scrollY, behavior: 'instant'}), fixedScroll);
    await expect.poll(async () => panel.evaluate((element) => getComputedStyle(element).position)).toBe('fixed');

    const bottomPinnedBox = await panel.boundingBox();
    expect(Math.abs(metrics.viewportHeight - bottomPinnedBox.y - bottomPinnedBox.height - 30)).toBeLessThan(2);

    const brandTrigger = desktopGroup(page, 1).getByTestId('catalog-filter-trigger');
    await brandTrigger.click();
    await expect(brandTrigger).toHaveAttribute('aria-expanded', 'false');
    await expect.poll(async () => panel.evaluate((element) => element.offsetHeight))
        .toBeLessThanOrEqual(metrics.viewportHeight - 50);
    await expect.poll(async () => panel.evaluate((element) => getComputedStyle(element).position)).toBe('fixed');

    const topPinnedBox = await panel.boundingBox();
    expect(Math.abs(topPinnedBox.y - 20)).toBeLessThan(2);
});

test('scrolls the catalog naturally when the mouse wheel is over the filter panel', async ({page}) => {
    const panel = page.locator('[data-panel="desktop"].catalog-filter-panel');
    const scroll = panel.locator('.catalog-filter-panel__scroll');
    await panel.hover();
    const initialPageScroll = await page.evaluate(() => window.scrollY);
    await page.mouse.wheel(0, 500);

    await expect.poll(async () => page.evaluate(() => window.scrollY)).toBeGreaterThan(initialPageScroll);
    expect(await scroll.evaluate((element) => element.scrollTop)).toBe(0);
});

test('uses an accessible multiple-open accordion with animated content', async ({page}) => {
    const panel = page.locator('[data-panel="desktop"]');
    const brandTrigger = desktopGroup(page, 1).getByTestId('catalog-filter-trigger');
    const priceTrigger = panel.locator('[data-filter-key="price"]').getByTestId('catalog-filter-trigger');
    const volumeTrigger = desktopGroup(page, 2).getByTestId('catalog-filter-trigger');
    const materialTrigger = desktopGroup(page, 4).getByTestId('catalog-filter-trigger');

    await expect(brandTrigger).toHaveAttribute('aria-expanded', 'true');
    await expect(priceTrigger).toHaveAttribute('aria-expanded', 'true');
    await expect(volumeTrigger).toHaveAttribute('aria-expanded', 'true');
    await expect(desktopGroup(page, 3).getByTestId('catalog-filter-trigger')).toHaveAttribute('aria-expanded', 'true');
    await volumeTrigger.click();
    await expect(priceTrigger).toHaveAttribute('aria-expanded', 'true');
    await expect(volumeTrigger).toHaveAttribute('aria-expanded', 'false');

    await materialTrigger.click();
    const content = desktopGroup(page, 4).getByTestId('catalog-filter-content');
    await expect(content).toBeVisible();
    expect(await content.evaluate((element) => getComputedStyle(element).animationName))
        .toBe('catalog-filter-accordion-open');

    await materialTrigger.focus();
    await page.keyboard.press('Enter');
    await expect(materialTrigger).toHaveAttribute('aria-expanded', 'false');
    await expect(materialTrigger).toBeFocused();
});

test('renders the desktop fallback in its final expanded state before Vue mounts', async ({browser}) => {
    const context = await browser.newContext({viewport: {width: 1440, height: 900}});
    const page = await context.newPage();
    await page.route('https://mc.yandex.ru/**', (route) => route.abort());
    await page.route('**/js/app.js*', (route) => route.fulfill({
        contentType: 'application/javascript',
        body: '',
    }));
    await page.goto(categoryUrl);

    const fallback = page.getByTestId('catalog-filters-fallback');
    await expect(fallback).toBeVisible();
    await expect(fallback.locator('details[data-property-id="1"]')).toHaveAttribute('open', '');
    await expect(fallback.locator('details[data-property-id="2"]')).toHaveAttribute('open', '');
    await expect(fallback.locator('details[data-property-id="3"]')).toHaveAttribute('open', '');
    await expect(fallback.locator('details[data-property-id="7"]')).not.toHaveAttribute('open', '');
    await context.close();
});

test('shows a contextual submit with the facet count next to the last desktop change', async ({page}) => {
    const contextSubmit = page.getByTestId('catalog-filter-context-submit');
    const option = desktopGroup(page, 1).locator('.catalog-filter-option').first();
    const input = option.getByTestId('catalog-filter-option');

    await expect(contextSubmit).toHaveCount(0);
    const firstFacetResponse = page.waitForResponse((response) => response.url().includes('/facets?'));
    await option.click();
    await expect(contextSubmit).toBeVisible();
    await expect(contextSubmit).toBeDisabled();
    await expect(contextSubmit).toHaveAttribute('aria-label', 'Считаем количество товаров');
    await expect(contextSubmit.getByTestId('catalog-filter-context-spinner')).toBeVisible();
    await expect(contextSubmit).not.toContainText('СЧИТАЕМ');
    await firstFacetResponse;
    await expect(contextSubmit).toBeEnabled();
    await expect(contextSubmit.getByTestId('catalog-filter-context-spinner')).toHaveCount(0);
    await expect(contextSubmit).toHaveText(/^ПОКАЗАТЬ \d+ (ТОВАР|ТОВАРА|ТОВАРОВ)$/);

    const optionBox = await option.boundingBox();
    const buttonBox = await contextSubmit.boundingBox();
    expect(buttonBox.x).toBeGreaterThan(optionBox.x + optionBox.width);
    expect(Math.abs((buttonBox.y + buttonBox.height / 2) - (optionBox.y + optionBox.height / 2))).toBeLessThan(2);
    expect(await contextSubmit.evaluate((element) => getComputedStyle(element).position)).toBe('fixed');

    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets')),
        option.click(),
    ]);
    await expect(input).not.toBeChecked();
    await expect(contextSubmit).toHaveCount(0);

    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?')),
        option.click(),
    ]);
    await Promise.all([
        page.waitForResponse((response) => new URL(response.url()).pathname === categoryUrl && response.request().resourceType() === 'fetch'),
        contextSubmit.click(),
    ]);
    await expect(contextSubmit).toHaveCount(0);
    await expect(page).toHaveURL(/properties/);
    await expect(page.locator('.card')).toHaveCount(24);
});

test('selects multiple values, updates facets and clears one group', async ({page}) => {
    const group = desktopGroup(page, 2);

    const options = group.locator('.catalog-filter-option');
    const first = options.nth(0).getByTestId('catalog-filter-option');
    const second = options.nth(1).getByTestId('catalog-filter-option');

    await options.nth(0).click();
    const responsePromise = page.waitForResponse((response) => response.url().includes('/facets?'));
    await options.nth(1).click();
    await responsePromise;

    await expect(first).toBeChecked();
    await expect(second).toBeChecked();
    await expect(group.locator('.catalog-filter-group__summary')).toHaveCount(0);
    await expect(group.locator('.catalog-filter-group__count')).toHaveCount(0);
    await expect(page.locator('[data-panel="desktop"] .catalog-filter-chip')).toHaveCount(0);
    await expect(group.getByText('Любой вариант', {exact: true})).toHaveCount(0);

    await options.nth(0).click();
    const clearResponse = page.waitForResponse((response) => response.url().includes('/facets'));
    await options.nth(1).click();
    await clearResponse;
    await expect(first).not.toBeChecked();
    await expect(second).not.toBeChecked();
});

test('keeps values from different groups and applies them through AJAX', async ({page}) => {
    const typeGroup = desktopGroup(page, 3);
    const brandGroup = desktopGroup(page, 1);

    const typeInput = typeGroup.getByTestId('catalog-filter-option').first();
    const typeValue = await typeInput.getAttribute('value');
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?')),
        typeInput.locator('..').click(),
    ]);

    await page.evaluate(() => window.scrollTo({top: 0, behavior: 'instant'}));
    await expect(brandGroup).toBeInViewport();

    const brandInput = brandGroup.getByTestId('catalog-filter-option').first();
    const brandValue = await brandInput.getAttribute('value');
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?')),
        brandInput.locator('..').click(),
    ]);

    const formData = await page.locator('#catalog-filter-form').evaluate((form) => (
        [...new FormData(form).entries()]
    ));
    expect(formData).toContainEqual(['properties[3][]', typeValue]);
    expect(formData).toContainEqual(['properties[1][]', brandValue]);

    await Promise.all([
        page.waitForResponse((response) => new URL(response.url()).pathname === categoryUrl && response.request().resourceType() === 'fetch'),
        page.getByTestId('catalog-filter-submit').click(),
    ]);

    await expect(page).toHaveURL(/properties/);
    const url = new URL(page.url());
    expect(url.searchParams.getAll('properties[3][]')).toEqual([typeValue]);
    expect(url.searchParams.getAll('properties[1][]')).toEqual([brandValue]);
    await expect(page.locator('.card')).toHaveCount(24);
});

test('keeps groups and the submit footer stable when facets become unavailable', async ({page}) => {
    const typeGroup = desktopGroup(page, 3);
    const submit = page.getByTestId('catalog-filter-submit');
    const panel = page.locator('[data-panel="desktop"]');
    const initialSubmitBox = await submit.boundingBox();
    const initialPanelBox = await panel.boundingBox();
    const initialGroupCount = await page.locator('[data-panel="desktop"] [data-testid="catalog-filter-dropdown"]').count();
    const initialOptionCount = await panel.getByTestId('catalog-filter-option').count();

    const electric = typeGroup.locator('[data-testid="catalog-filter-option"][value="6"]');
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?')),
        electric.locator('..').click(),
    ]);

    await expect(electric).toBeChecked();
    await expect(electric).toBeEnabled();
    await expect(page.locator('[data-panel="desktop"] [data-testid="catalog-filter-dropdown"]')).toHaveCount(initialGroupCount);
    await expect(panel.getByTestId('catalog-filter-option')).toHaveCount(initialOptionCount);
    await expect(panel.locator('.catalog-filter-option--disabled').first()).toBeVisible();
    await expect(panel.locator('.catalog-filter-option--disabled input').first()).toBeDisabled();
    const updatedSubmitBox = await submit.boundingBox();
    const updatedPanelBox = await panel.boundingBox();
    const initialFooterGap = initialPanelBox.y + initialPanelBox.height - initialSubmitBox.y - initialSubmitBox.height;
    const updatedFooterGap = updatedPanelBox.y + updatedPanelBox.height - updatedSubmitBox.y - updatedSubmitBox.height;
    expect(Math.abs(updatedPanelBox.height - initialPanelBox.height)).toBeLessThan(2);
    expect(Math.abs(updatedFooterGap - initialFooterGap)).toBeLessThan(2);
});

test('reveals additional properties without duplicating filter logic', async ({page}) => {
    const panel = page.locator('[data-panel="desktop"]');
    const toggle = panel.getByTestId('catalog-filter-additional');

    await expect(panel.getByTestId('catalog-filter-dropdown')).toHaveCount(6);
    await toggle.click();
    await expect(toggle).toHaveAttribute('aria-expanded', 'true');
    await expect(panel.getByTestId('catalog-filter-dropdown')).toHaveCount(23);
    await toggle.click();
    await expect(panel.getByTestId('catalog-filter-dropdown')).toHaveCount(6);
});

test('filters by price and persists the range in the Laravel GET URL', async ({page}) => {
    const minInput = page.locator('[data-panel="desktop"] [data-testid="catalog-price-min"]');

    await minInput.fill('200000');
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?price_min=200000')),
        minInput.press('Tab'),
    ]);

    await expect(page.locator('[data-panel="desktop"] .catalog-filter-chip')).toHaveCount(0);
    await expect(minInput).toHaveValue('200000');
    await Promise.all([
        page.waitForResponse((response) => new URL(response.url()).pathname === categoryUrl && response.url().includes('price_min=200000')),
        page.getByTestId('catalog-filter-submit').click(),
    ]);
    await expect(page).toHaveURL(/price_min=200000/);
    await page.reload();
    await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', 'noindex,follow');
});

test('shows a compact mobile toolbar before products', async ({page}) => {
    await page.setViewportSize({width: 390, height: 844});
    await page.reload();

    await expect(page.getByTestId('catalog-filter-mobile-open')).toBeVisible();
    await expect(page.locator('.catalog-filter-desktop')).toBeHidden();
    const gridBox = await page.getByTestId('catalog-products-grid').boundingBox();
    expect(gridBox.y).toBeLessThan(400);
    await expect(page.locator('.card')).toHaveCount(24);
});

test('opens an accessible full-screen mobile drawer, locks the page and returns focus', async ({page}) => {
    await page.setViewportSize({width: 390, height: 844});
    await page.reload();
    const opener = page.getByTestId('catalog-filter-mobile-open');

    await opener.click();
    const dialog = page.getByTestId('catalog-filter-mobile-dialog');
    await expect(dialog).toBeVisible();
    await expect(dialog.locator('input[type="search"]')).toHaveCount(0);
    const box = await dialog.boundingBox();
    expect(Math.abs(box.width - 390)).toBeLessThan(1);
    expect(Math.abs(box.height - 844)).toBeLessThan(1);
    expect(await page.evaluate(() => getComputedStyle(document.body).overflow)).toBe('hidden');

    await page.keyboard.press('Escape');
    await expect(dialog).toBeHidden();
    await expect(opener).toBeFocused();
});

test('keeps mobile choices as a draft and updates products only after Apply', async ({page}) => {
    await page.setViewportSize({width: 390, height: 844});
    await page.reload();
    let catalogRequests = 0;
    page.on('request', (request) => {
        const url = new URL(request.url());
        if (url.pathname === categoryUrl && request.resourceType() === 'fetch') {
            catalogRequests++;
        }
    });

    await page.getByTestId('catalog-filter-mobile-open').click();
    const group = mobileGroup(page, 3);
    const option = group.getByTestId('catalog-filter-option').first();
    const value = await option.getAttribute('value');

    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?')),
        option.locator('..').click(),
    ]);
    expect(catalogRequests).toBe(0);
    await expect(page).not.toHaveURL(/properties/);

    await Promise.all([
        page.waitForResponse((response) => new URL(response.url()).pathname === categoryUrl && response.request().resourceType() === 'fetch'),
        page.getByTestId('catalog-filter-mobile-dialog').getByTestId('catalog-filter-submit').click(),
    ]);
    await expect(page.getByTestId('catalog-filter-mobile-dialog')).toBeHidden();
    expect(new URL(page.url()).searchParams.getAll('properties[3][]')).toEqual([value]);
    expect(catalogRequests).toBe(1);

    await expect(page.locator('.catalog-filter-chips--mobile')).toHaveCount(0);
    await page.getByTestId('catalog-filter-mobile-open').click();
    const dialog = page.getByTestId('catalog-filter-mobile-dialog');
    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets')),
        dialog.getByTestId('catalog-filter-mobile-reset').click(),
    ]);
    await Promise.all([
        page.waitForResponse((response) => new URL(response.url()).pathname === categoryUrl && response.request().resourceType() === 'fetch'),
        dialog.getByTestId('catalog-filter-submit').click(),
    ]);
    await expect(page).not.toHaveURL(/properties/);
    expect(catalogRequests).toBe(2);
});

test('discards unsubmitted mobile draft values when the drawer closes', async ({page}) => {
    await page.setViewportSize({width: 390, height: 844});
    await page.reload();
    await page.getByTestId('catalog-filter-mobile-open').click();
    const group = mobileGroup(page, 3);
    const value = await group.getByTestId('catalog-filter-option').first().getAttribute('value');

    await Promise.all([
        page.waitForResponse((response) => response.url().includes('/facets?')),
        group.locator(`[data-testid="catalog-filter-option"][value="${value}"]`).locator('..').click(),
    ]);
    await page.getByRole('button', {name: 'Закрыть фильтры'}).click();
    await page.getByTestId('catalog-filter-mobile-open').click();
    const reopenedGroup = mobileGroup(page, 3);
    await expect(reopenedGroup.locator(`[data-testid="catalog-filter-option"][value="${value}"]`)).not.toBeChecked();
});

test('renders 24 products and changes pages through AJAX', async ({page}) => {
    await expect(page.locator('.card')).toHaveCount(24);
    await expect(page.locator('.catalog-pagination__link--active')).toHaveText('1');
    await page.locator('#catalog-filter-app').evaluate((element) => {
        element.dataset.persistenceMarker = 'pagination';
    });

    await Promise.all([
        page.waitForResponse((response) => response.url().includes('page=2') && response.request().resourceType() === 'fetch'),
        page.locator('[data-catalog-pagination] a[aria-label="Страница 2"]').click(),
    ]);
    await expect(page).toHaveURL(/page=2/);
    await expect(page.locator('.card')).toHaveCount(24);
    await expect(page.locator('.catalog-pagination__link--active')).toHaveText('2');
    await expect(page.locator('#catalog-filter-app')).toHaveAttribute('data-persistence-marker', 'pagination');
});

test('changes sorting through AJAX without remounting the filter', async ({page}) => {
    await page.locator('#catalog-filter-app').evaluate((element) => {
        element.dataset.persistenceMarker = 'sorting';
    });

    await Promise.all([
        page.waitForResponse((response) => new URL(response.url()).searchParams.get('sort') === 'price' && response.request().resourceType() === 'fetch'),
        page.locator('[data-catalog-sort] a').filter({hasText: 'Дешевле'}).click(),
    ]);

    await expect(page).toHaveURL(/sort=price/);
    await expect(page.locator('.card')).toHaveCount(24);
    await expect(page.locator('#catalog-filter-app')).toHaveAttribute('data-persistence-marker', 'sorting');
});

test('uses self canonical pagination and noindexes filtered SSR pages', async ({page}) => {
    await page.goto(`${categoryUrl}?page=2`);
    await expect(page.locator('link[rel="canonical"]')).toHaveAttribute('href', /page=2/);
    await expect(page).toHaveTitle(/страница 2/);

    await page.goto(`${categoryUrl}?properties%5B3%5D%5B%5D=6`);
    await expect(page.locator('meta[name="robots"]')).toHaveAttribute('content', 'noindex,follow');
    await expect(page.locator('link[rel="canonical"]')).not.toHaveAttribute('href', /properties/);
});

test('keeps a compact functional SSR fallback when JavaScript is disabled', async ({browser}) => {
    const context = await browser.newContext({javaScriptEnabled: false, viewport: {width: 390, height: 844}});
    const page = await context.newPage();
    await page.route('https://mc.yandex.ru/**', (route) => route.abort());
    await page.goto(categoryUrl);

    const fallback = page.getByTestId('catalog-filters-fallback');
    await expect(fallback).toBeVisible();
    await expect(fallback.locator('.catalog-filter-group')).toHaveCount(24);
    await expect(fallback.locator('details[data-property-id="1"]')).not.toHaveAttribute('open', '');
    await expect(fallback.locator('details[data-property-id="2"]')).not.toHaveAttribute('open', '');
    await expect(fallback.locator('details[data-property-id="3"]')).not.toHaveAttribute('open', '');
    await expect(fallback.locator('details[data-property-id="7"]')).not.toHaveAttribute('open', '');
    const initialOptionCount = await fallback.locator('input[name^="properties"]').count();
    await expect(fallback.locator('input[name="price_min"]')).toBeVisible();
    await expect(page.locator('.card')).toHaveCount(24);
    await expect(page.locator('[data-catalog-pagination] a[aria-label="Страница 2"]')).toHaveAttribute('href', /page=2/);
    await expect(page.locator('script[defer]')).toHaveCount(3);

    const gridBox = await page.getByTestId('catalog-products-grid').boundingBox();
    expect(gridBox.y).toBeLessThan(900);

    await page.goto(`${categoryUrl}?properties%5B3%5D%5B%5D=6`);
    const filteredFallback = page.getByTestId('catalog-filters-fallback');
    await expect(filteredFallback.locator('input[name^="properties"]')).toHaveCount(initialOptionCount);
    expect(await filteredFallback.locator('.catalog-filter-option--disabled input:disabled').count()).toBeGreaterThan(0);
    const selectedOption = filteredFallback.locator('input[name="properties[3][]"][value="6"]');
    await expect(selectedOption).toBeChecked();
    await expect(selectedOption).toBeEnabled();
    await context.close();
});
