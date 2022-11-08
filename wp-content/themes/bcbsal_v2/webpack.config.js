/**
 * Webpack Configuration
 * @package square
 * @author Rhythm Shahriar
 * @link https://rhy.io
 * @version 2.0.0
 */

const webpack = require('webpack');
const browserSync = require('browser-sync');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CopyWebpackPlugin = require('copy-webpack-plugin')
const TerserPlugin = require("terser-webpack-plugin");
const ImageminWebpWebpackPlugin= require("imagemin-webp-webpack-plugin");


const APP = __dirname + '/src/bundle.js';
const BUILD = __dirname + '/assets';

//check current development environment
const isProd = process.env.NODE_ENV === "production";

/**
 * BrowserSync configuration
 * @type {{host: string, port: number, proxy: string}}
 */
if (!isProd) {
  browserSync.init({
    injectChanges: true
  })
}

const settings = {
  host: 'localhost',
  port: 8080,
  open: 'external',
  proxy: 'http://localhost:8080'
}

module.exports = {
  mode: isProd ? 'production' : 'development',
  entry: APP,
  devtool: isProd ? false : 'eval',
  output: {
    path: BUILD,
    filename: 'bundle.js',
  },
  performance: {
    hints: false
  },
  module: {
    rules: [
      {
        test: /\.(s[ac]|c)ss$/i,
        use: [
          /**
           *  For production mode, use MiniCssExtractPlugin to extract CSS into a separate file.
           *  For development mode, use style-loader to inject CSS into the DOM.
           */
          {
            loader: isProd ? MiniCssExtractPlugin.loader : "style-loader",
          },
          "css-loader",
          "postcss-loader",
          "sass-loader",
        ],
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
        },
      },
      {
        test: /\.(ttf|eot|woff|woff2)$/,
        type: 'asset/resource',
        exclude: /node_modules/,
        generator: {
          filename: './fonts/[name][ext]',
        },
      }
    ]
  },
  optimization: {
    minimizer: [
      new TerserPlugin({
        extractComments: false,
      })
    ],
  },
  plugins: [
    /**
     * BrowserSyncPlugin
     * @ref https://www.npmjs.com/package/browser-sync-webpack-plugin
     */
    new BrowserSyncPlugin({
      host: settings.host,
      port: settings.port,
      proxy: settings.proxy,
      notify: false,
      files: [__dirname + '/*.php', __dirname + '/src/*.js', __dirname + '/src/js/*.js', __dirname + '/src/scss/*.scss']
    }),

    /**
     * MiniCssExtractPlugin
     * @ref https://webpack.js.org/plugins/mini-css-extract-plugin/
     */
    new MiniCssExtractPlugin({
      filename: "bundle.css",
    }),

    /**
     * Copy all the font & image files
     * jpeg, jpg, png, gif, svg
     * @ref https://webpack.js.org/plugins/copy-webpack-plugin/
     */
    new CopyWebpackPlugin({
      patterns: [
        {
          from: __dirname + '/src/images',
          to: __dirname + '/assets/images',
        }
      ],
    }),
    /**
     * Load Jquery/$ globally
     */
    new webpack.ProvidePlugin(
      {
        $: 'jquery',
        jQuery: 'jquery'
      }
    ),
    /**
     * Convert jpg, jpeg, png to webp
     */
    new ImageminWebpWebpackPlugin({
      config: [{
        test: /\.(jpe?g|png)/,
        options: {
          quality:  90
        }
      }],
      overrideExtension: true,
      detailedLogs: false,
      silent: false,
      strict: true
    }),
  ]
}
