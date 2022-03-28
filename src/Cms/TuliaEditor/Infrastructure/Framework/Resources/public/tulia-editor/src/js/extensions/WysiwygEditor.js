const ClassObserver = require('../shared/Utils/ClassObserver.js');
const AbstractTuliaEditorExtension = require('./AbstractTuliaEditorExtension.js');

module.exports = class WysiwygEditor extends AbstractTuliaEditorExtension {
    createVueComponent () {
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
    }
};
