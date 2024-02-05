const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');
const TerserPlugin = require("terser-webpack-plugin");
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */
module.exports = {
    optimization: {
        minimize: true,
        minimizer: [new TerserPlugin()],
    },
};

mix
    .sass('resources/css/backend/layouts/app.scss', 'public/css/backend')
    .js('resources/js/backend/app.js', 'public/js/backend')
    .sourceMaps()
    .extract()
    .vue({
        version: 3
    })
    .mergeManifest()

if (mix.inProduction()) {
    mix.version();
}

mix.browserSync({
    proxy: 'http://127.0.0.1:8000',
});
