const path = require('path');
const glob = require('glob');
const webpack = require('webpack');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const PurgecssPlugin = require('purgecss-webpack-plugin');

const PATHS = {
    src: path.join(__dirname, 'resources/views')
};

module.exports = {
    mode: 'development',
    entry: [
        './resources/js/app.js'
    ],
    output: {
        filename: 'js/app.js',
        path: path.resolve(__dirname, 'public'),
    },
    plugins: [
        new MiniCssExtractPlugin({
            // Options similar to the same options in webpackOptions.output
            // all options are optional
            filename: 'css/style.css',
            chunkFilename: '[id].css',
            ignoreOrder: false, // Enable to remove warnings about conflicting order
        }),
        new webpack.ProvidePlugin({
            $: "jquery",
            jQuery: "jquery",
            "window.jQuery": "jquery'",
            "window.$": "jquery"
        }),
        // new PurgecssPlugin({
        //     paths: glob.sync(`${PATHS.src}/**`, {nodir: true}),
        // }),

    ],
    module: {
        rules: [
            {
                test: /\.(sa|sc|c)ss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader,
                        options: {
                            publicPath: '../css/',
                        },
                    },
                    'css-loader',
                    'sass-loader'
                ],
            },
            {
                test: /\.(jpe?g|png|gif)$/i,
                loader:"file-loader",
                options:{
                    name:'[name].[ext]',
                    outputPath:'images/'
                    //the images will be emited to dist/assets/images/ folder
                }
            }
        ],
    },
};