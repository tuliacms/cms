<template>
    <div><WysiwygEditor v-model="block.data.text" :blockId="block.id"></WysiwygEditor></div>
</template>

<script setup>
const WysiwygEditor = require('extensions/WysiwygEditor.vue').default;
const { defineProps, inject } = require('vue');

/****************
 * Prepare block
 ****************/
const props = defineProps(require('./props.js').default);
const blocks = inject('blocks');
const block = blocks.editor('core-textblock', props);
</script>
