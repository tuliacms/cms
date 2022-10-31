'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');

module.exports = {
    mode: 'development',
    entry: './src/js/script.js',
    output: {
        filename: 'backend-theme.bundle.min.js',
        path: path.resolve(__dirname, 'dist'),
        library: {
            name: 'Tulia',
            type: 'var',
            export: 'default',
        },
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                use: 'vue-loader'
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    'sass-loader'
                ]
            },
            {
                test: /\.css$/i,
                use: [MiniCssExtractPlugin.loader, "css-loader"],
            },
        ]
    },
    watchOptions: {
        aggregateTimeout: 200,
        poll: 1000,
        ignored: ['**/dist', '**/node_modules'],
    },
    resolve: {
        extensions: ['.js', '.scss', '.jpg']
    },
    devtool: 'source-map',
    plugins: [
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin(),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    delete: ['./dist/translations', './dist/images' ],
                    copy: [
                        { source: './src/js/translations', destination: './dist/translations' },
                        { source: './src/images', destination: './dist/images' },
                        { source: './dist', destination: './../../../../../../../../../public/assets/core/backend/theme' },
                    ],
                },
            },
        }),
    ]
};

if (process.env.NODE_ENV === 'production') {
    module.exports.devtool = 'source-map';
    module.exports.optimization = {
        minimize: true
    };
    // http://vue-loader.vuejs.org/en/workflow/production.html
    module.exports.plugins = (module.exports.plugins || []).concat([
        new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }),
        new webpack.LoaderOptionsPlugin({
            minimize: true
        })
    ]);
}
