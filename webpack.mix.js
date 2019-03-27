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

mix
/* CSS */
    .js('resources/laravel/js/app.js', 'public/assets/js/app.js')
    .js('resources/laravel/js/form-facturas.js', 'public/assets/js/form-facturas.js')
    .sass('resources/gull/assets/styles/sass/themes/eva.scss', 'public/assets/styles/css/themes/eva.min.css')

/* JS */
/* Laravel JS */

mix.combine([
    'resources/gull/assets/js/vendor/jquery-3.3.1.min.js',
    'resources/gull/assets/js/vendor/bootstrap.bundle.min.js',
    'resources/gull/assets/js/vendor/perfect-scrollbar.min.js',
    'resources/laravel/js/utility.js', 'public/assets/js/etax-utility.js',
], 'public/assets/js/common-bundle.js');

mix.js([
    'resources/gull/assets/js/script.js',
], 'public/assets/js/script.js');

//mix.js('resources/laravel/js/form-facturas.js', 'public/assets/js/form-facturas.js');