var webpack = require('webpack');
// var path    = require('path');



console.log( 'ENV', process.env.NODE_ENV );
console.log('RUNNIGN NODE_ENV:', process.env.NODE_ENV==='production' ? process.env.NODE_ENV : 'dev');

module.exports = {
  context: __dirname,
  devtool: "inline-sourcemap",
  entry: "./src/app.js",
  resolve: {
    modulesDirectories: ['node_modules', 'src'], // Folders where Webpack is going to look for files to bundle together
    extensions: ['', '.js'] // Extensions that Webpack is going to expect
   
  },
  module: {
    // Loaders allow you to preprocess files as you require() or “load” them. Loaders are kind of like “tasks” in other build tools, and provide a powerful way to handle frontend build steps.
    loaders: [
        {
          test: /\.js$/,
          exclude: /(node_modules|bower_components)/,
          loader: 'babel-loader',
          query: { presets: ['es2015', 'react'], plugins: ["transform-decorators-legacy", "transform-class-properties"] }
        }
    ]
  },
  output: {
    path: __dirname + "/public/js/build/",
    filename: "bundle.min.js"
  },
  plugins: process.env.NODE_ENV === 'production' ? [
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.OccurrenceOrderPlugin(),
    new webpack.optimize.UglifyJsPlugin(),
    new webpack.DefinePlugin({
      "process.env": { 
         NODE_ENV: JSON.stringify("production") 
       }
    })
  ] : [],
};
