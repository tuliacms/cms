<template>
    <span
        contenteditable="true"
        class="tued-ext-contenteditable-element"
        ref="editable"
        @keydown="event => preventHtml(event)"
        @input="onChange"
        @paste="onPaste"
        @focusin="preventSelfUpdate = true"
        @focusout="preventSelfUpdate = false"
        :data-placeholder="translator.trans('startTypingPlaceholder')"
    ></span>
</template>

<style scoped lang="scss">
.tued-ext-contenteditable-element {
    display: inline-block;
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

<script setup>
const { defineProps, inject, defineEmits, ref, watch, computed, onMounted } = require('vue');
const props = defineProps({
    modelValue: {
        required: true,
        default: ''
    }
});
const emit = defineEmits(['update:modelValue']);
const messenger = inject('messenger');
const translator = inject('translator');
const preventSelfUpdate = ref(false);

const editable = ref(props.modelValue);

const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

watch(model, async (newValue) => {
    if (preventSelfUpdate.value === false) {
        editable.value.innerHTML = newValue;
    }
});

const preventHtml = (e) => {
    if (e.ctrlKey) {
        if (e.key === 'c' || e.key === 'v' || e.key === 'x' || e.key === 'a' || e.key === 'z') {
            return;
        }
    } else {
        return;
    }

    e.preventDefault();
    return false;
};

const onPaste = (e) => {
    const data = (e.clipboardData || window.clipboardData).getData('Text');

    insertTextAtCaret(data);

    e.preventDefault();
    return false;
};

const insertTextAtCaret = (text) => {
    text = text.replace(/<\/?[^>]+(>|$)/g, '');

    if (window.getSelection) {
        let sel = window.getSelection();

        if (sel.getRangeAt && sel.rangeCount) {
            let range = sel.getRangeAt(0);
            range.deleteContents();
            range.insertNode(document.createTextNode(text));
            onChange();
        }
    } else if (document.selection && document.selection.createRange) {
        document.selection.createRange().text = text;
        onChange();
    }
};

const onChange = () => {
    model.value = editable.value.innerHTML;
};

onMounted(() => {
    editable.value.innerHTML = model.value;
});

</script>
<script>
export default {name: 'Extension.Contenteditable.Editor'}
</script>
