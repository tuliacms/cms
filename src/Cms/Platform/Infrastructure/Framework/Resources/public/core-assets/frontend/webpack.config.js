'use strict';

const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');
const fs = require('fs');

let config = {
    mode: 'development',
    entry: './src/default-script.js',
    output: {
        filename: 'default-frontend.js',
        path: path.resolve(__dirname, 'dist'),
    },
    module: {
        rules: [
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
        extensions: ['.js', '.scss'],
    },
    externals: {
        $: '$',
    },
    devtool: 'source-map',
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'default-frontend.css',
        }),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    copy: [
                        { source: './dist', destination: './../../../../../../../../../../public/assets/core/frontend' },
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
