'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');
const fs = require('fs');

let config = {
    mode: 'development',
    entry: './src/js/websites-builder.js',
    output: {
        filename: 'websites-builder.js',
        path: path.resolve(__dirname, 'dist'),
        library: {
            name: 'TuliaWebsiteBuilder',
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
        Tulia: 'Tulia',
        bootstrap: 'bootstrap',
    },
    resolve: {
        extensions: ['.js', '.scss'],
    },
    devtool: 'source-map',
    plugins: [
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin(),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    copy: [
                        { source: './dist/**/*', destination: './../../../../../../../../../public/assets/core/tulia-websites-builder' },
                    ],
                },
            },
        }),
    ]
};

module.exports = (env, argv) => {
    if (argv.mode === 'production') {
        config.optimization = {
            minimize: true
        };
        config.plugins.push(new webpack.DefinePlugin({
            'process.env': {
                NODE_ENV: '"production"'
            }
        }));
        config.plugins.push(new webpack.LoaderOptionsPlugin({
            minimize: true
        }));
    }

    return config;
};
