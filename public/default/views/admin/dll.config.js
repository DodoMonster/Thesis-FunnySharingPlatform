const webpack = require('webpack');

const vendors = [
    "vue",
    "jquery",
    "vue-router",
    "hdp-vue-components"
];

module.exports = {
    output: {
        path: 'static',
        filename: '[name].js',
        library: '[name]',
    },
    entry: {
        "lib": vendors,
    },
    plugins: [
        new webpack.DllPlugin({
            path: 'manifest.json',
            name: '[name]',
            context: __dirname,
        }),
    ],
};