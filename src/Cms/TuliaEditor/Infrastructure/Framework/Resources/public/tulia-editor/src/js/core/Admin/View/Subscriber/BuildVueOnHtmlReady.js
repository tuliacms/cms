import Admin from "admin/Admin.vue";

export default class BuildVueOnHtmlReady {
    constructor(vueFactory, options, instanceId, directives, controls, extensions, blocks, container) {
        this.vueFactory = vueFactory;
        this.options = options;
        this.instanceId = instanceId;
        this.directives = directives;
        this.controls = controls;
        this.extensions = extensions;
        this.blocks = blocks;
        this.container = container;
    }

    static getSubscribedEvents() {
        return {
            'admin.ready': 'build',
        };
    }

    build() {
        const vue = this.vueFactory.factory(
            Admin,
            { container: this.container },
            this.directives,
            this.controls,
            this.extensions,
            this.blocks,
        );

        vue.mount(`#tued-editor-window-inner-${this.instanceId}`);
    }
}
