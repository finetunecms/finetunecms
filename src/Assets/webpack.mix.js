const { mix } = require('laravel-mix');

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

mix.js('./source/js/app.js', './js')
    .extract(['vue', 'lodash', 'vue-resource', 'vue-sortable', 'vuestrap-base-components','vue-select', 'vue-upload-component', 'moment'])
    .sourceMaps();


mix.sass('./source/sass/app.scss', './css');