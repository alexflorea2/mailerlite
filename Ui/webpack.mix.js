const mix = require("laravel-mix");

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
mix.webpackConfig((webpack) => {
    return {
        resolve: {
            alias: {
                "@": path.resolve(__dirname , 'js')
            }
        }
    };
});

mix.js("js/app.js", "dist/js").sourceMaps();
// .extract()
// .sourceMaps();

mix.sass("sass/style.scss", "dist/css");
    // .sourceMaps();

mix.setPublicPath('dist');
