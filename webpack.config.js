const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/js/app.js')
    .addEntry('vue', './assets/vue/main.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableVueLoader()
    .configureBabel(() => {}, {
        useBuiltIns: 'usage',
        corejs: 3,
    })
    .enableSassLoader()
    .enablePostCssLoader((options) => {
        options.config = {
            path: './postcss.config.js'
        };
    });
module.exports = Encore.getWebpackConfig();
