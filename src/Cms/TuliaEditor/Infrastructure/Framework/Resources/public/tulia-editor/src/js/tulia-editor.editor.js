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
            template: `<div class="tued-container">
                <Structure :structure="structure" :messenger="messenger"></Structure>
                <RenderingCanvas ref="rendered-content" :structure="structure"></RenderingCanvas>
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
                structure: ObjectCloner.deepClone(options.structure.source),
            },
            mounted() {
                this.$root.$on('block.inner.updated', () => {
                    this.messenger.send('structure.synchronize.from.editor', this.structure);
                });

                this.messenger.listen('structure.rendered.fetch', () => {
                    this.messenger.send(
                        'structure.rendered.data',
                        this.$root.$refs['rendered-content'].$el.innerHTML
                    );
                });
                this.messenger.listen('structure.synchronize.from.admin', (structure) => {
                    this.structure = structure;
                    this.messenger.send('structure.updated');
                });

                document.addEventListener('click', (event) => {
                    if (event.target.tagName === 'HTML') {
                        this.messenger.send('structure.selection.deselected');
                    }
                });
            }
        });
    });

    messenger.send('editor.init.fetch');
});
