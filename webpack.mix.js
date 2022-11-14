const mix = require('laravel-mix')
const path = require('path')

mix.js('resources/js/main.js', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .options({
            processCssUrls: false,
            postCss: []
    })