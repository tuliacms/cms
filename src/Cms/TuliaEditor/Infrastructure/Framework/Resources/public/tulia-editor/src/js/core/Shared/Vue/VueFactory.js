const Vue = require('vue');
import { createPinia } from 'pinia';

export default class VueFactory {
    factory(rootComponent, data, directives, controls, extensions, blocks) {
        const vue = Vue.createApp(rootComponent, data);

        vue.use(createPinia());

        VueFactory.loadDirectives(vue, directives);
        VueFactory.loadControls(vue, controls);
        VueFactory.loadExtensions(vue, extensions);
        VueFactory.loadBlocks(vue, blocks);

        // DEV
        //vue.config.devtools = true;
        //vue.config.performance = true;
        // PROD
        vue.config.devtools = false;
        vue.config.debug = false;
        vue.config.silent = true;

        return vue;
    }

    static loadDirectives (vueApp, directives) {
        for (let i in directives) {
            vueApp.directive(i, directives[i]);
        }
    }

    static loadControls (vueApp, controls) {
        for (let i in controls) {
            vueApp.component(i, controls[i]);
        }
    }

    static loadExtensions (vueApp, extensions) {
        for (let i in extensions) {
            if (extensions[i].Manager) {
                vueApp.component(i + 'Manager', extensions[i].Manager);
            }
        }
    }

    static loadBlocks (vueApp, blocks) {
        for (let i in blocks) {
            vueApp.component('block-' + blocks[i].code + '-manager', blocks[i].manager);
            vueApp.component('block-' + blocks[i].code + '-editor', blocks[i].editor);
            vueApp.component('block-' + blocks[i].code + '-render', blocks[i].render);
        }
    }
}
