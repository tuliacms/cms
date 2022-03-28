import './../css/tulia-editor.editor.scss';
import draggable from 'vuedraggable';
import Structure from './admin/Vue/Components/Editor/Structure/Structure.vue';
import RenderingCanvas from './admin/Vue/Components/Editor/Rendering/Canvas.vue';
import Messenger from './shared/Messenger';
import ClassObserver from './shared/Utils/ClassObserver';
import VueComponents from './shared/VueComponents.js';
import Location from './shared/Utils/Location.js';
import ObjectCloner from './shared/Utils/ObjectCloner';

window.TuliaEditor = {};
window.TuliaEditor.blocks = [];
window.TuliaEditor.extensions = {};

window.TuliaEditor.registerExtensions = function () {
    for (let i in TuliaEditor.extensions) {
        TuliaEditor.extensions[i].call();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    let instanceId = Location.getQueryVariable('tuliaEditorInstance');
    let messenger = new Messenger(instanceId, window.parent, 'editor');

    messenger.listen('editor.init.data', function (options) {
        Vue.config.devtools = true;

        TuliaEditor.registerExtensions();

        VueComponents.registerBlocksAsComponents(TuliaEditor.blocks);

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

class AbstractTuliaEditorBlock {
    static name = '';

    static data () {
        return {};
    }

    static template () {
        return '<div></div>';
    }

    static render () {
        return '<div></div>';
    }
}

class TextBlock extends AbstractTuliaEditorBlock {
    static name = 'core-textblock';

    static data () {
        return {
            text: '<p>Some default text :(</p>'
        };
    }

    static template () {
        return '<div><WysiwygEditor v-model="text"></WysiwygEditor></div>';
    }

    static render () {
        return '<div v-html="text"></div>';
    }
}

window.TuliaEditor.blocks.push(TextBlock);

TuliaEditor.extensions['WysiwygEditor'] = function () {
    return Vue.component('WysiwygEditor', {
        props: {
            value: {
                type: String,
                required: true,
                default: ''
            },
        },
        data() {
            return {
                quill: null,
            };
        },
        template:`<div>
            <div class="editor-toolbar">
                <span class="ql-formats">
                    <button class="ql-bold" title="Bold <ctrl+b>"></button>
                    <button class="ql-italic" title="Italic <ctrl+i>"></button>
                    <button class="ql-underline" title="Underline <ctrl+u>"></button>
                    <button class="ql-strike" title="Strikethrough"></button>
                </span>
                <span class="ql-formats">
                    <button class="ql-script" value="sub" title="Subscript"></button>
                    <button class="ql-script" value="super" title="Superscript"></button>
                </span>
                <span class="ql-formats">
                    <button class="ql-header" value="1"></button>
                    <button class="ql-header" value="2"></button>
                    <button class="ql-blockquote" title="Blockquote"></button>
                    <button class="ql-code-block" title="Code block"></button>
                </span>
                <span class="ql-formats">
                    <button class="ql-list" value="ordered"></button>
                    <button class="ql-list" value="bullet"></button>
                </span>
                <span class="ql-formats">
                    <select class="ql-align" title="Align text"></select>
                    <button class="ql-link" title="Link text"></button>
                    <button class="ql-clean" title="Clean text formatting"></button>
                </span>
            </div>
            <div class="editor-container"><slot></slot></div>
        </div>`,
        mounted () {
            let editorContent = this.$el.getElementsByClassName('editor-container')[0];
            let editorToolbar = this.$el.getElementsByClassName('editor-toolbar')[0];
            editorContent.innerHTML = this.value ?? null;

            let quill = new Quill(editorContent, {
                theme: 'bubble',
                placeholder: 'Start typing...',
                modules: {
                    toolbar: editorToolbar,
                }
            });
            quill.on('text-change', () => {
                this.$emit('input', quill.root.innerHTML);
                this.$root.$emit('block.inner.updated');
            });

            this.quill = quill;

            new ClassObserver(quill.theme.tooltip.root, 'ql-hidden', (currentClass) => {
                if(currentClass) {
                    this.$root.$emit('structure.selection.show');
                } else {
                    this.$root.$emit('structure.selection.hide');
                }
            });
        },
        watch: {
            value (val) {
                if (this.quill.root.innerHTML === val) {
                    return;
                }

                this.quill.root.innerHTML = val ? val : '';
            }
        },
        /*destroyed () {
            $(this.$el).chosen('destroy');
        }*/
    });
};
