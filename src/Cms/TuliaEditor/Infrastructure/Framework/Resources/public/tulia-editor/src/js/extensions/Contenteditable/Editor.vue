<template>
    <span
        contenteditable="true"
        class="tued-ext-contenteditable-element"
        ref="editable"
        @keydown="event => preventHtml(event)"
        @input="_changed"
        @paste="onPaste"
        :data-placeholder="this.translator.trans('startTypingPlaceholder')"
    ></span>
</template>

<style scoped lang="scss">
.tued-ext-contenteditable-element {
    display: block;
    outline: none !important;

    &:hover {
        outline: 1px dotted #8c8c8c !important;
    }

    &:empty:before {
        content: attr(data-placeholder);
        color: rgba(0,0,0,.3);
        font-style: italic;
        display: inline-block;
    }
}
</style>

<script>
export default {
    props: {
        modelValue: {
            required: true,
            default: ''
        }
    },
    name: 'Contenteditable',
    inject: ['messenger', 'translator'],
    methods: {
        onPaste (e) {
            const data = (e.clipboardData || window.clipboardData).getData('Text');

            this.insertTextAtCaret(data);

            e.preventDefault();
            return false;
        },
        insertTextAtCaret(text) {
            text = text.replace(/<\/?[^>]+(>|$)/g, '');

            if (window.getSelection) {
                let sel = window.getSelection();

                if (sel.getRangeAt && sel.rangeCount) {
                    let range = sel.getRangeAt(0);
                    range.deleteContents();
                    range.insertNode(document.createTextNode(text));
                    this._changed();
                }
            } else if (document.selection && document.selection.createRange) {
                document.selection.createRange().text = text;
                this._changed();
            }
        },
        preventHtml (e) {
            if (e.ctrlKey) {
                if (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'a' || e.key === 'z') {
                    return;
                }
            } else {
                return;
            }

            e.preventDefault();
            return false;
        },
        _changed () {
            this.$emit('update:modelValue', this.$refs.editable.innerHTML);
        }
    },
    mounted () {
        this.$refs.editable.innerHTML = this.modelValue;
    }
};
</script>
