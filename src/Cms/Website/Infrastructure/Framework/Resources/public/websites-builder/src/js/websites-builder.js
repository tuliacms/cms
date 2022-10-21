const Root = require('./components/Root.vue').default;
const Vue = require('vue');

export default class {
    vue;

    constructor (selector, data) {
        this.vue = Vue.createApp(Root, data);
        this.vue.mount(selector);
    }
}
