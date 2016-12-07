var path = require('path');
var webpack = require('webpack');

module.exports = {
    entry: {
        main: "./src/main.js"
    },
    output: {
        path: './static',
        publicPath: '/views/thesis/static/',
        filename: 'main.js'
    },
    module: {
        // 特定规则的文件与特定loader的映射
        loaders: [{
                test: /\.vue$/,
                loader: 'vue'
            }, {
                test: /\.scss$/,
                loaders: ["style", "css", "sass"]
            }, {　　　　　　
                test: /\.tpl$/,
                loader: 'html-withimg-loader'　　　　
            }, {
                test: /\.(jpe?g|png|gif|svg)$/i,
                // inline base64url for <=1500 images
                loader: 'url-loader?limit=8192&name=images/[hash:8].[name].[ext]'
            }, {
                test: /\.js$/,
                exclude: /libs|node_modules|vue\/dist|vue-router\/|vue-loader\/|vue-hot-reload-api\//,
                loader: 'babel',
                query: {
                    // since babel 6 needs preset to determin what to transform
                    presets: ['es2015'],
                    plugins: ['transform-runtime']
                }
            },
            //  {
            //     test: /jquery\.datetimepicker/,
            //     loader: 'imports?jQuery=jquery,$=jquery,this=>window'
            // }
        ]
    },

    // 配置各个loader参数
    sassLoader: {
        // includePaths, An array of paths that LibSass can look in to attempt to resolve your @import declarations.
        includePaths: [path.resolve(__dirname, "./scss")]
    },

    resolve: {
        root: [path.resolve(__dirname, "./src")]
    },

    // plugins: [
    //     new webpack.DllReferencePlugin({
    //         context: __dirname,
    //         manifest: require('./manifest.json'),
    //     })
    // ]
};

if (process.env.NODE_ENV === 'production') {
    // module.exports.plugins = [
    //     new webpack.DefinePlugin({
    //         'process.env': {
    //             NODE_ENV: '"production"'
    //         }
    //     }),
    //     new webpack.optimize.UglifyJsPlugin({
    //         compress: {
    //             warnings: false
    //         }
    //     }),
    //     new webpack.optimize.OccurenceOrderPlugin()
    // ]
} else {
    module.exports.devtool = '#source-map'
}
