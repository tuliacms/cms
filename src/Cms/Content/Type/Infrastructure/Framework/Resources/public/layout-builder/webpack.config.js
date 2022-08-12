'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');
const fs = require('fs');

module.exports = {
  entry: './src/js/main.js',
  mode: 'development',
  output: {
    path: path.resolve(__dirname, './dist/js'),
    publicPath: '/dist/js/',
    filename: 'build.js'
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
    TuliaEditor: 'TuliaEditor',
    Tulia: 'Tulia',
    lodash: '_',
  },
  resolve: {
    extensions: ['.js', '.scss'],
  },
  devtool: 'source-map',
  plugins: [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin(),
    //new webpack.BannerPlugin(fs.readFileSync('./LICENSE', 'utf8')),
    new FileManagerPlugin({
      events: {
        onEnd: {
          copy: [
            { source: './dist/css/*', destination: './../../../../../../../../../../public/assets/core/content-builder-layout-builder/css' },
            { source: './dist/js/*', destination: './../../../../../../../../../../public/assets/core/content-builder-layout-builder/js' },
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




/*
let path = require('path');
let webpack = require('webpack');
let { VueLoaderPlugin } = require('vue-loader');

module.exports = {
  entry: './src/js/main.js',
  mode: 'development',
  output: {
    path: path.resolve(__dirname, './dist/js'),
    publicPath: '/dist/js/',
    filename: 'build.js'
  },
  module: {
    rules: [
      {
        test: /\.sass$/,
        use: [
          'vue-style-loader',
          'css-loader',
          'sass-loader?indentedSyntax'
        ],
      },
      {
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            // Since sass-loader (weirdly) has SCSS as its default parse mode, we map
            // the "scss" and "sass" values for the lang attribute to the right configs here.
            // other preprocessors should work out of the box, no loader config like this necessary.
            'scss': [
              'vue-style-loader',
              'css-loader',
              'sass-loader'
            ],
            'sass': [
              'vue-style-loader',
              'css-loader',
              'sass-loader?indentedSyntax'
            ]
          }
        }
      }
    ]
  },
  plugins: [
    new VueLoaderPlugin()
  ],
  resolve: {
    alias: {
      'vue$': 'vue/dist/vue.esm.js'
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  devServer: {
    historyApiFallback: true,
    noInfo: true,
    overlay: true
  },
  performance: {
    hints: false
  },
  devtool: '#eval-source-map'
};

if (process.env.NODE_ENV === 'production') {
  module.exports.devtool = '#source-map';
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
*/
