const {defineConfig} = require('@playwright/test');

module.exports = defineConfig({
    testDir: './tests/Ui',
    fullyParallel: false,
    workers: 1,
    reporter: 'list',
    use: {
        baseURL: process.env.PLAYWRIGHT_BASE_URL || 'http://127.0.0.1:8080',
        channel: 'chrome',
        headless: true,
        trace: 'retain-on-failure',
        viewport: {width: 1440, height: 1000},
    },
});
