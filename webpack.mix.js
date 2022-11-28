let mix = require('laravel-mix');
let webpack = require('webpack');
// let path = require('path')

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

let laravel_root = path.resolve(__dirname);

let myMix = mix.js('resources/assets/js/app.js', 'public/js')
   .less('resources/assets/less/app.less', 'public/css');

if (process.node.NODE_ENV !== 'production') {
   myMix.webpackConfig({ devtool: "inline-source-map" });
   myMix.version();
}
else {
	myMix.webpackConfig({
		plugins: [
			new webpack.DefinePlugin({
				'process.env': {
					NODE_ENV: '"production"',
				}
			}),
		]
	});
}
