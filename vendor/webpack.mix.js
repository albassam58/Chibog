const mix = require('laravel-mix');

require('laravel-mix-vue-auto-routing');

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
mix.webpackConfig({
	resolve: {
		alias: {
			"@env": path.resolve(__dirname, "vue-env"),
			"@pages": path.resolve(__dirname, "resources/js/pages"),
			"@components": path.resolve(__dirname, "resources/js/components")
		}
	}
})
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ])
    .vueAutoRouting()
    .extract(['vue']);
