const Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('app', './assets/js/app.js')
    .addEntry('form', './assets/js/form.js')
    .addEntry('profile', './assets/js/profile.js')
    .addEntry('spreadsheet', './assets/js/spreadsheet.js')
    .addEntry('storage', './assets/js/storage.js')
    .addEntry('users', './assets/js/users.js')

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // enable single runtime
    .enableSingleRuntimeChunk()

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()
;

module.exports = Encore.getWebpackConfig();
