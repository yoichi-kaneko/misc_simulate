const mix = require('laravel-mix');

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
const webpack = require('webpack');
mix.webpackConfig({
    plugins: [
        // reduce bundle size by ignoring moment js local files
        new webpack.IgnorePlugin({resourceRegExp: /\.\/locale$/})
    ]
});
mix.sass('resources/sass/vendors.scss', 'public/css')
    .sass('resources/sass/katniss.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css')
    .js('resources/js/vendors.js', 'public/js')
    .js('resources/js/katniss.js', 'public/js')
    .js('resources/js/ResizeSensor.js', 'public/js')
    .js('resources/js/main.js', 'public/js')
    .js('resources/js/pages/centipede.js', 'public/js/pages')
    .js('resources/js/pages/nash.js', 'public/js/pages')

mix.copyDirectory('resources/images', 'public/images');

if (mix.inProduction()) {
    mix.version();
}
