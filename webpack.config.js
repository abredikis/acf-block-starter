const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const WebpackAssetsManifest = require('webpack-assets-manifest');
const path = require("path");

module.exports = {
	...defaultConfig,
	plugins: [
		...defaultConfig.plugins,
		new MiniCssExtractPlugin({
			filename: "[name].css"
		}),
		new WebpackAssetsManifest({
			writeToDisk: true,
			output: "manifest.json",
			entrypoints: true,
		})
	],
	module: {
		...defaultConfig.module,
		rules: [
			{
				test: /\.(sa|sc|c)ss$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					{
						loader: 'postcss-loader',
						options: {
							postcssOptions: {
								plugins: [
									require('autoprefixer')(), // You can specify browserlist options here if needed
								],
							},
						},
					},
					'sass-loader', // Add this line for processing SCSS
				],
			},
			{
				test: /\.(png|svg|jpg|jpeg|gif|webp)$/i,
				type: 'asset/resource',
			},
			{
				test: /\.(woff|woff2|eot|ttf|otf)$/i,
				type: 'asset/resource',
			}
		]
	},
	optimization: {
		...defaultConfig.optimization,
		moduleIds: 'size',
		runtimeChunk: 'single',
		splitChunks: {
			chunks: 'all',
			minSize: 1,
			cacheGroups: {
				defaultVendors: {
					test: /[\\/]node_modules[\\/]/,
					priority: -10,
					reuseExistingChunk: true,
				},
				styles: {
					test: /[\\/]styles[\\/]/,
					priority: -10,
					reuseExistingChunk: true,
				},
				scripts: {
					test: /[\\/]scripts[\\/]/,
					priority: -10,
					reuseExistingChunk: true,
				},
				default: {
					minChunks: 1,
					priority: -20,
					reuseExistingChunk: true,
				},
			},
		},
	},
};
