import './../css/tulia-editor.editor.scss';
import draggable from 'vuedraggable';
import TuliaEditorInstance from './admin/Vue/TuliaEditorInstance.vue';
import Messanger from './shared/Messenger';
import ClassObserver from './shared/Utils/ClassObserver';

window.TuliaEditor = {};
window.TuliaEditor.blocks = [];
window.TuliaEditor.extensions = {};

window.TuliaEditor.registerBlocks = function () {
    for (let i in TuliaEditor.blocks) {
        let data = TuliaEditor.blocks[i].data();
        let dataBinds = [];
        let props = [];
        let watches = {};

        for (let property in data) {
            dataBinds.push(` :${property}="blockData.${property}"`);
            props.push(property);
            watches[property] = function (value) {
                this.$emit('value-changed', property, value);
            }
        }

        Vue.component(TuliaEditor.blocks[i].name + '-component-frame', {
            props: ['blockData'],
            template: `<div><component :is="'${TuliaEditor.blocks[i].name}'" ${dataBinds.join()} @value-changed="updateBlockData"></component></div>`,
            methods: {
                updateBlockData (property, value) {
                    this.blockData[property] = value;
                }
            }
        });

        Vue.component(TuliaEditor.blocks[i].name, {
            props: props,
            data () {
                return data;
            },
            watch: watches,
            template: `<div
                @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'block')"
                @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'block')"
                @mousedown="$root.$emit('structure.selectable.select', $el, 'block')"
            >
                ${TuliaEditor.blocks[i].template()}
            </div>`
        });
    }
};

window.TuliaEditor.registerExtensions = function () {
    for (let i in TuliaEditor.extensions) {
        TuliaEditor.extensions[i].call();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    function getQueryVariable(variable) {
        let query = window.location.search.substring(1);
        let vars = query.split('&');

        for (let i = 0; i < vars.length; i++) {
            let pair = vars[i].split('=');

            if (decodeURIComponent(pair[0]) === variable) {
                return decodeURIComponent(pair[1]);
            }
        }

        console.error('Query variable %s not found', variable);
    }

    let instanceId = getQueryVariable('tuliaEditorInstance');
    let messanger = new Messanger(instanceId, window.parent, 'editor');

    messanger.listen('editor.init.data', function (options) {
        Vue.config.devtools = true;

        TuliaEditor.registerExtensions();
        TuliaEditor.registerBlocks();

        /*Vue.directive('bs-tooltip', function(el) {
            let tooltip = new bootstrap.Tooltip(el);
            tooltip.enable();
        });*/

        Vue.use(draggable);

        new Vue({
            el: '#tulia-editor',
            template: '<TuliaEditorInstance :instanceId="instanceId" :options="options" :messanger="messanger" :availableBlocks="availableBlocks"></TuliaEditorInstance>',
            components: {
                TuliaEditorInstance
            },
            data: {
                instanceId: instanceId,
                options: options,
                messanger: messanger,
                availableBlocks: TuliaEditor.blocks
            }
        });
    });

    messanger.send('editor.init.fetch');
});

class AbstractTuliaEditorBlock {
    static name = '';

    static data () {
        return {};
    }

    static template () {
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
}

window.TuliaEditor.blocks.push(TextBlock);

TuliaEditor.extensions['WysiwygEditor'] = function () {
    return Vue.component('WysiwygEditor', {
        props: {
            value: [String, null],
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
                },
                bounds: this.$el.closest('.tued-structure')
            });
            quill.on('text-change', () => {
                this.$emit('input', quill.root.innerHTML);
                this.$root.$emit('block.inner.updated');
            });

            new ClassObserver(quill.theme.tooltip.root, 'ql-hidden', (currentClass) => {
                if(currentClass) {
                    this.$root.$emit('structure.selectable.show');
                } else {
                    this.$root.$emit('structure.selectable.hide');
                }
            });
        },
        watch: {
            content (val) {
                this.$el.getElementsByClassName('editor-container')[0].innerHTML = val;
            }
        },
        /*destroyed () {
            $(this.$el).chosen('destroy');
        }*/
    });
};
