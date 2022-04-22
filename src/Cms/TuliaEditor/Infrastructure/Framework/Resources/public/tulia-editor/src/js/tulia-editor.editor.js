import './../css/tulia-editor.editor.scss';

const Vue = require('vue');
const Messenger = require('shared/Messaging/Messenger.js').default;
const Location = require('shared/Utils/Location.js').default;
const Translator = require('shared/I18n/Translator.js').default;
const EditorRoot = require("components/Editor/Root.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const extensions = require("extensions/extensions.js").default;
const blocks = require("blocks/blocks.js").default;

export class Canvas {
    instanceId = null;
    vue = null;

    constructor () {
        this.instanceId = Location.getQueryVariable('tuliaEditorInstance');

        this.start();
    }

    start () {
        let self = this;
        let messenger = new Messenger(this.instanceId, 'editor', [window, window.parent]);

        messenger.on('editor.ready', () => {
            messenger.execute('editor.init.fetch').then((options) => {
                self.vue = Vue.createApp(EditorRoot, {
                    container: {
                        translator: new Translator(
                            options.locale,
                            options.fallback_locales,
                            options.translations
                        ),
                        messenger: messenger,
                    },
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
        });
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
