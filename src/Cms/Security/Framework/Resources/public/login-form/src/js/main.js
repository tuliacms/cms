const LoginForm = require('./LoginForm.vue').default;
const Vue = require('vue');

import './../sass/login.scss';

$(function () {
    let vue = Vue.createApp(LoginForm);
    // DEV
    //vue.config.devtools = true;
    //vue.config.performance = true;
    // PROD
    vue.config.devtools = false;
    vue.config.debug = false;
    vue.config.silent = true;
    vue.mount('#login-form');
});
