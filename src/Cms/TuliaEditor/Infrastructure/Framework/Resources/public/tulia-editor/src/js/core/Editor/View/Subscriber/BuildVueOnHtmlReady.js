import Editor from "editor/Editor.vue";

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
            'editor.view.ready': 'build',
        };
    }

    build() {
        const vue = this.vueFactory.factory(
            Editor,
            { container: this.container },
            this.directives,
            this.controls,
            this.extensions,
            this.blocks,
        );

        vue.mount(`#tulia-editor`);
    }
}
