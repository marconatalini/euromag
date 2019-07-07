var Encore = require('@symfony/webpack-encore');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')



    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.scss)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')

    /*
         * ENTRY CONFIG
         *
         * Add 1 entry for each "page" of your app
         * (including one that's included on every page - e.g. "app")
         *
         * Each entry will result in one JavaScript file (e.g. app.js)
         * and one CSS file (e.g. app.scss) if you JavaScript imports CSS.
         */
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/ubicazioni_update', './assets/js/ubicazioni_update.js')
    .addEntry('js/optimize', './assets/js/optimize.js')

    .addStyleEntry('css/app', './assets/css/app.css')
    .addStyleEntry('css/home', './assets/css/home.css')
    .addStyleEntry('css/ubicazioni', './assets/css/ubicazioni.css')
    .addStyleEntry('css/articoli', './assets/css/articoli.css')
    .addStyleEntry('css/optimize', './assets/css/optimize.css')

    //.addStyleEntry('css/app', ['./assets/css/app.css'])
    //.addEntry('page1', './assets/js/page1.js')
    //.addEntry('page2', './assets/js/page2.js')

    .copyFiles({
        from: './assets/images',
        // optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        //to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg|svg)$/
        })
;



// Use polling instead of inotify
const config = Encore.getWebpackConfig();
config.watchOptions = {
        poll: true,
};

// Export the final configuration
module.exports = config;


// module.exports = Encore.getWebpackConfig();
