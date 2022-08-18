const App = require('./App.vue').default;
const Vue = require('vue');

vue = Vue.createApp(App);
vue.config.devtools = true;
vue.config.performance = true;
vue.mount('#contact-form-builder');
