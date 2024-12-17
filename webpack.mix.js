const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
mix.js('resources/js/app.js', 'public/js')
    // .js('resources/js/mix-scripts.js' ,'public/js/theme.js')
    .vue({ version: 3 })
    .sass('resources/sass/style.scss', 'public/css/app.css')
    .webpackConfig(require('./webpack.config'));

    mix.scripts([
        'resources/js/theme/components/*.js',
        'resources/js/theme/layout/*.js',
        // 'resources/js/theme/custom/utilities/modals/*/*.js',
        'resources/js/theme/custom/*.js',
        // 'resources/js/theme/vendors/plugins/*.js',
        // 'resources/js/theme/widgets/*/*.js'
    ], 'public/js/theme.js');

    mix.browserSync({
        proxy: 'http://localhost:8000'
    })

if (mix.inProduction()) {
    mix.version();
}