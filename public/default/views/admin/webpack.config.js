var path = require('path');
var webpack = require('webpack');

module.exports = {
    entry: "./src/entry.js",
    output: {
        path: './static',
        publicPath: '/views/admin/static/',
        filename: 'main.js'
    },
    module: {
        loaders: [
            {
                test: /\.vue$/,
                loader: 'vue'
            },
            {
                test: /\.scss$/,
                loaders: ["style", "css", "sass"] 
            },
            {
                test: /\.js$/,
                loader: 'babel',
                exclude: /node_modules|vue\/dist|vue-router\/|vue-loader\/|vue-hot-reload-api\//,
                query: {
                    // since babel 6 needs preset to determin what to transform
                    presets: ['es2015']
                }
            },{
                test:/\.(jpe?g|png|gif|svg)$/i,
                loader:'url-loader?limit=1500&name=images/[name].[hash].[ext]'
            }
        ]
    },

    resolve: {
        root: [path.resolve(__dirname, "./src")]
    },

    plugins: [
        new webpack.DllReferencePlugin({
            context: __dirname,
            manifest: require('./manifest.json'),
        })
    ]
};


if (process.env.NODE_ENV === 'production') {
    module.exports.plugins = module.exports.plugins.concat([
        new webpack.optimize.UglifyJsPlugin({
            compress: {
                warnings: false
            }
        }),
        new webpack.optimize.OccurenceOrderPlugin()
    ])
} else {
    module.exports.devtool = '#source-map'
}