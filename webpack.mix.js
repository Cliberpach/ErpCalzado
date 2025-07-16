const mix = require('laravel-mix');
const path = require('path');

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

mix.js('resources/js/app.js', 'public/js').vue()
    .sass('resources/sass/app.scss', 'public/css');

mix.js('resources/js/appNotify.js', 'public/js').vue()
    .sass('resources/sass/appNotify.scss', 'public/css');

mix.js('resources/js/appPages.js', 'public/js').vue()
    .sass('resources/sass/appPages.scss', 'public/css');

mix.js('resources/js/utilidades.js', 'public/js');

mix.webpackConfig({
    resolve: {
        alias: {
            '@sass': path.resolve(__dirname, 'resources/sass'),
            'jquery': require.resolve('jquery')
        },
        fallback: {
            os: require.resolve('os-browserify/browser')
        }
    }
}).sourceMaps()

mix.disableNotifications();
