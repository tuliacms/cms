import './../css/tulia-editor.editor.scss';
import draggable from 'vuedraggable';
import Structure from './admin/Vue/Components/Editor/Structure/Structure.vue';
import RenderingCanvas from './admin/Vue/Components/Editor/Rendering/Canvas.vue';
import Messenger from './shared/Messenger.js';
import Location from './shared/Utils/Location.js';
import ObjectCloner from './shared/Utils/ObjectCloner.js';

import TuliaEditor from './TuliaEditor.js';
window.TuliaEditor = TuliaEditor;

document.addEventListener('DOMContentLoaded', function () {
    let instanceId = Location.getQueryVariable('tuliaEditorInstance');
    let messenger = new Messenger(instanceId, window.parent, 'editor');

    messenger.listen('editor.init.data', function (options) {
        Vue.config.devtools = true;

        TuliaEditor.loadExtensions();
        TuliaEditor.loadBlocks();

        Vue.use(draggable);

        new Vue({
            el: '#tulia-editor',
            template: `<div class="tued-container" ref="root">
                <Structure :structure="currentStructure" :messenger="messenger"></Structure>
                <RenderingCanvas ref="rendered-content" :structure="currentStructure"></RenderingCanvas>
            </div>`,
            components: {
                Structure,
                RenderingCanvas
            },
            data: {
                instanceId: instanceId,
                options: options,
                messenger: messenger,
                availableBlocks: TuliaEditor.blocks,
                currentStructure: {},
                previousStructure: {},
            },
            methods: {
                restorePreviousStructure: function () {
                    this.currentStructure = ObjectCloner.deepClone(this.previousStructure);
                },
                useCurrentStructureAsPrevious: function () {
                    this.previousStructure = ObjectCloner.deepClone(this.currentStructure);
                }
            },
            mounted() {
                this.currentStructure = ObjectCloner.deepClone(this.options.structure.source);
                this.previousStructure = ObjectCloner.deepClone(this.options.structure.source);

                messenger.listen('editor.structure.fetch', () => {
                    this.useCurrentStructureAsPrevious();

                    messenger.send(
                        'editor.structure.data',
                        this.currentStructure,
                        this.$root.$refs['rendered-content'].$el.innerHTML
                    );
                });

                messenger.listen('editor.structure.restore', () => {
                    this.restorePreviousStructure();
                    messenger.send('editor.structure.restored');
                    messenger.send('structure.changed', this.currentStructure);
                });

                document.addEventListener('click', (event) => {
                    if (event.target.tagName === 'HTML') {
                        messenger.send('structure.selection.deselected');
                    }
                });
            },
            watch: {
                currentStructure: {
                    handler(newValue, oldValue) {
                        messenger.send('structure.changed', this.currentStructure);
                    },
                    deep: true
                }
            }
        });
    });

    messenger.send('editor.init.fetch');
});
