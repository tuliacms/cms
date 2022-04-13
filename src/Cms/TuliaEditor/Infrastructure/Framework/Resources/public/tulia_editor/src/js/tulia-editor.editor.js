import './../css/tulia-editor.editor.scss';

const Vue = require('vue');
const Messenger = require('shared/Messenger.js').default;
const Location = require('shared/Utils/Location.js').default;
const EventDispatcher = require('shared/EventDispatcher.js').default;
const EditorRoot = require("components/Editor/Root.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const extensions = require("extensions/extensions.js").default;
const blocks = require("blocks/blocks.js").default;

export class Canvas {
    instanceId = null;
    messenger = null;
    eventDispatcher = null;
    vue = null;

    constructor () {
        this.instanceId = Location.getQueryVariable('tuliaEditorInstance');
        this.container = {
            messenger: new Messenger(this.instanceId, window.parent, 'editor'),
            eventDispatcher: new EventDispatcher(),
        };

        this.start();
    }

    start () {
        let self = this;

        this.container.messenger.listen('editor.init.data', function (options) {
            self.vue = Vue.createApp(EditorRoot, {
                container: self.container,
                instanceId: self.instanceId,
                options: options,
                availableBlocks: blocks,
                structure: ObjectCloner.deepClone(options.structure.source)
            });
            self.loadExtensions(self.vue);
            self.loadBlocks(self.vue);
            self.vue.config.devtools = true;
            self.vue.config.performance = true;

            self.vue.mount('#tulia-editor');
        });

        this.container.messenger.send('editor.init.fetch');
    }

    loadExtensions (vueApp) {
        for (let i in extensions) {
            vueApp.component(i, extensions[i]);
        }
    }

    loadBlocks (vueApp) {
        for (let i in blocks) {
            vueApp.component('block-' + blocks[i].code + '-editor', blocks[i].editor);
            vueApp.component('block-' + blocks[i].code + '-render', blocks[i].render);
        }
    }
}
