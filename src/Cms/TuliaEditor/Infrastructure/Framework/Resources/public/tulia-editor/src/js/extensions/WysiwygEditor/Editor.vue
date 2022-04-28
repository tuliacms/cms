<template>
    <div>
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
        <div class="editor-container">
            <slot></slot>
        </div>
    </div>
</template>

<style scoped lang="scss">
.editor-container {
    outline: none !important;

    &:hover {
        outline: 1px dotted #555 !important;
    }
}
</style>

<script>
const ClassObserver = require('shared/Utils/ClassObserver.js').default;

export default {
    props: {
        modelValue: {
            required: true,
            default: ''
        },
        blockId: {
            required: true,
            default: ''
        },
    },
    inject: ['messenger', 'translator'],
    data () {
        return {
            quill: null,
        };
    },
    mounted () {
        let editorContent = this.$el.getElementsByClassName('editor-container')[0];
        let editorToolbar = this.$el.getElementsByClassName('editor-toolbar')[0];
        editorContent.innerHTML = this.modelValue ?? null;

        let quill = new Quill(editorContent, {
            theme: 'bubble',
            placeholder: this.translator.trans('startTypingPlaceholder'),
            modules: {
                toolbar: editorToolbar,
            }
        });
        quill.on('text-change', () => {
            this.$emit('update:modelValue', quill.root.innerHTML);
            this.messenger.notify('structure.element.updated', this.blockId);
        });

        this.quill = quill;

        /*new ClassObserver(quill.theme.tooltip.root, 'ql-hidden', (currentClass) => {
            if(currentClass) {
                this.$eventDispatcher.emit('structure.selection.show');
            } else {
                this.$eventDispatcher.emit('structure.selection.hide');
            }
        });*/

        this.messenger.on('editor.click.outside', () => {
            quill.theme.tooltip.root.classList.add('ql-hidden');
        });
    },
    watch: {
        modelValue (val) {
            if (this.quill.root.innerHTML === val) {
                return;
            }

            this.quill.root.innerHTML = val ? val : '';
        }
    },
    /*destroyed () {
        $(this.$el).chosen('destroy');
    }*/
};
</script>
