const RoutableContentTypeApp = require('./RoutableContentTypeApp.vue').default;
const ContentBlockApp = require('./ContentBlockApp.vue').default;
const Vue = require('vue');

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
        vue.config.devtools = true;
        vue.config.performance = true;
        vue.mount('#content-builder-routable-content-type-builder');
    }
    if (document.querySelector('#content-builder-content-block-builder')) {
        vue = Vue.createApp(ContentBlockApp);
        vue.config.devtools = true;
        vue.config.performance = true;
        vue.mount('#content-builder-content-block-builder');
    }
});
