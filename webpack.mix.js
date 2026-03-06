const mix = require('laravel-mix');
const path = require('path');
const { BundleAnalyzerPlugin } = require('webpack-bundle-analyzer');


mix.js('resources/js/app.js', 'public/js').vue()
    .sass('resources/sass/app.scss', 'public/css')
    .sass('resources/sass/fontawesome.scss', 'public/css');

mix.js('resources/js/appNotify.js', 'public/js').vue()
    .sass('resources/sass/appNotify.scss', 'public/css');

mix.js('resources/js/appPages.js', 'public/js').vue()
    .sass('resources/sass/appPages.scss', 'public/css');

mix.js('resources/js/utilidades.js', 'public/js');

mix.js('resources/js/filepond.js', 'public/js')
   .postCss('resources/css/filepond.css', 'public/css');

mix.options({
    processCssUrls: false
});

mix.copyDirectory('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/webfonts');

mix.webpackConfig({
    resolve: {
        alias: {
            '@sass': path.resolve(__dirname, 'resources/sass'),
            'jquery': require.resolve('jquery')
        },
        fallback: {
            os: require.resolve('os-browserify/browser')
        }
    },
    plugins: [
        new BundleAnalyzerPlugin()
    ]
}).sourceMaps()

mix.disableNotifications();

if (mix.inProduction()) {
    mix.version();
}
