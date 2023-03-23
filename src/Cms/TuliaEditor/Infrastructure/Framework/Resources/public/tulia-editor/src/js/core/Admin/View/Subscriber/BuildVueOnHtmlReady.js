import Admin from "admin/Admin.vue";

export default class BuildVueOnHtmlReady {
    constructor(vueFactory, options, instanceId, container) {
        this.vueFactory = vueFactory;
        this.options = options;
        this.instanceId = instanceId;
        this.container = container;
    }

    build() {
        const vue = this.vueFactory.factory(
            Admin,
            { container: this.container },
            this.options.directives,
            this.options.controls,
            this.options.extensions,
            this.options.blocks,
        );

        vue.mount(`#tued-editor-window-inner-${this.instanceId}`);
    }
}
