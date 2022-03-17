import TuliaEditorVue from './admin/Vue/TuliaEditor.vue'

window.TuliaEditor = function (selector, options) {
    this.selector = selector;
    this.options = options;

    this.init = function () {
        let self = this;

        new Vue({
            render: h => h(TuliaEditorVue),
            data() {
                return {
                    options: self.options
                }
            }
        }).$mount(this.selector);
    };

    this.init();
};

