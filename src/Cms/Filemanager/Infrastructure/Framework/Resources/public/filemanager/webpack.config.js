'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');
const fs = require('fs');

module.exports = {
    mode: 'development',
    entry: './src/js/main.js',
    output: {
        filename: 'tulia-filemanager.js',
        path: path.resolve(__dirname, 'dist'),
        library: {
            name: 'TuliaFilemanager',
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
    externals: {
        vue: 'Vue',
        TuliaEditor: 'TuliaFilemanager',
        Tulia: 'Tulia',
        lodash: '_',
    },
    resolve: {
        extensions: ['.js', '.scss'],
        alias: {
            components: path.resolve(__dirname, 'src/js/components'),
            shared: path.resolve(__dirname, 'src/js/shared'),
        }
    },
    devtool: 'source-map',
    plugins: [
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin(),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    copy: [
                        { source: './dist/**/*', destination: './../../../../../../../../../public/assets/core/filemanager' },
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
