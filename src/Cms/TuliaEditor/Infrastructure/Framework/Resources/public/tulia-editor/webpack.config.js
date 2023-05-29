'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');
const fs = require('fs');

let config = {
    mode: 'development',
    entry: './src/js/tulia-editor.js',
    output: {
        filename: 'tulia-editor.js',
        path: path.resolve(__dirname, 'dist'),
        library: {
            name: 'TuliaEditor',
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
        ],
    },
    watchOptions: {
        aggregateTimeout: 200,
        poll: 1000,
        ignored: ['**/dist', '**/node_modules'],
    },
    externals: {
        vue: 'Vue',
        TuliaEditor: 'TuliaEditor',
        Tulia: 'Tulia',
        TuliaFilemanager: 'TuliaFilemanager',
        lodash: '_',
        Popper: 'Popper',
    },
    resolve: {
        extensions: ['.js', '.scss'],
        alias: {
            core: path.resolve(__dirname, 'src/js/core'),
            admin: path.resolve(__dirname, 'src/js/vue/Admin'),
            editor: path.resolve(__dirname, 'src/js/vue/Editor'),
            components: path.resolve(__dirname, 'src/js/Components'),
            blocks: path.resolve(__dirname, 'src/js/blocks'),
            extensions: path.resolve(__dirname, 'src/js/extensions'),
            controls: path.resolve(__dirname, 'src/js/vue/controls'),
            directives: path.resolve(__dirname, 'src/js/vue/directives'),
        }
    },
    devtool: 'source-map',
    plugins: [
        new VueLoaderPlugin(),
        new MiniCssExtractPlugin(),
        new webpack.BannerPlugin(fs.readFileSync('./LICENSE', 'utf8')),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    copy: [
                        { source: './dist', destination: './../../../../../../../../../public/assets/core/tulia-editor' },
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
