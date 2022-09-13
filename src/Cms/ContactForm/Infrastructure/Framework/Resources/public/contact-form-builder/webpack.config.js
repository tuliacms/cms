'use strict';

const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const FileManagerPlugin = require('filemanager-webpack-plugin');
const path = require('path');

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
      }
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
  plugins: [
    new VueLoaderPlugin(),
    new FileManagerPlugin({
      events: {
        onEnd: {
          copy: [
            { source: './dist', destination: './../../../../../../../../../public/assets/core/contact-forms' },
          ],
        },
      },
    }),
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
  devtool: 'source-map',
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
