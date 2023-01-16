const RoutableContentTypeApp = require('./RoutableContentTypeApp.vue').default;
const ContentBlockApp = require('./ContentBlockApp.vue').default;
const Vue = require('vue');

import '../sass/style.scss';

/*
Vue.directive('bs-tooltip', function(el) {
    let tooltip = new bootstrap.Tooltip(el);
    tooltip.enable();
});
*/

$(function () {
    let vue;

    if (document.querySelector('#content-builder-routable-content-type-builder')) {
        vue = Vue.createApp(RoutableContentTypeApp);
        // DEV
        //vue.config.devtools = true;
        //vue.config.performance = true;
        // PROD
        vue.config.devtools = false;
        vue.config.debug = false;
        vue.config.silent = true;
        vue.mount('#content-builder-routable-content-type-builder');
    }
    if (document.querySelector('#content-builder-content-block-builder')) {
        vue = Vue.createApp(ContentBlockApp);
        // DEV
        //vue.config.devtools = true;
        //vue.config.performance = true;
        // PROD
        vue.config.devtools = false;
        vue.config.debug = false;
        vue.config.silent = true;
        vue.mount('#content-builder-content-block-builder');
    }
});
