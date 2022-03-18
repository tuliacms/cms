const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');
const FileManagerPlugin = require('filemanager-webpack-plugin');

module.exports = {
    entry: {
        'js/tulia-editor.admin.js': './src/js/tulia-editor.admin.js',
        'js/tulia-editor.editor.js': './src/js/tulia-editor.editor.js',
        'js/tulia-editor.preview.js': './src/js/tulia-editor.preview.js',
    },
    output: {
        filename: '[name]',
        path: path.resolve(__dirname, 'dist'),
    },
    mode: 'development',
    watch: true,
    watchOptions: {
        aggregateTimeout: 200,
        poll: 1000,
        ignored: ['**/dist', '**/node_modules'],
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.tsx?$/,
                loader: 'ts-loader',
                exclude: /node_modules/,
                options: { appendTsSuffixTo: [/\.vue$/] }
            },
            {
                test: /\.s[ac]ss$/i,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    'sass-loader'
                ]
            }
        ]
    },
    resolve: {
        extensions: ['.ts', '.js', '.scss']
    },
    plugins: [
        new VueLoaderPlugin(),
        new FileManagerPlugin({
            events: {
                onEnd: {
                    copy: [
                        { source: './dist/**/*', destination: './../../../../../../../../../public/assets/core/tulia-editor-new' },
                    ],
                },
            },
        }),
    ],
    devtool: 'eval-source-map'
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
