const webpack = require('webpack');

const vendors = [
    "vue",
    "jquery",
    "vue-router",
    "es6-promise"
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