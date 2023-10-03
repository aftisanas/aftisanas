let mix = require('laravel-mix');
const path = require('path');

mix.setPublicPath('assets');
mix.setResourceRoot('../');

// compiling js
mix.js([
    'js/main.js', 
    'js/bootstrap.min.js', 
    'js/imagesloaded.pkgd.min.js', 
    'js/jquery-2.1.3.min.js',
    'js/jquery.hoverdir.js',
    'js/jquery.magnific-popup.min.js',
    'js/jquery.shuffle.min.js',
    'js/masonry.pkgd.min.js',
    'js/modernizr.custom.js',
    'js/owl.carousel.min.js',
    'js/page-transition.js',
    'js/validator.js'
], 'js/scripts.js');

// compiling css
mix.styles([
    'css/animate.css',
    'css/bootstrap.css',
    'css/bootstrap.min.css',
    'css/font-awesome.css',
    'css/magnific-popup.css',
    'css/main.css',
    'css/normalize.css',
    'css/owl.carousel.css',
    'css/pe-icon-7-stroke.css',
    'css/transition-animations.css'
], 'css/styles.css');

// compiling images
mix.copy('src/images/**/*', 'assets/images');

mix.webpackConfig({ resolve: { fallback: 
    {
        "path": require.resolve("path-browserify"),
        "fs": false,
        "os": false,
        'generate': false,
        'lib/generate-banner': false,
        'package': false,
    }  } });