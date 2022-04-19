<template>
    <div><WysiwygEditor v-model="block.data.text"></WysiwygEditor></div>
</template>

<script setup>
const WysiwygEditor = require('extensions/WysiwygEditor.vue').default;
const { defineProps, inject } = require('vue');

/****************
 * Prepare block
 ****************/
const props = defineProps(['id', 'data']);
const blocks = inject('blocks');
const block = blocks.editor(
    'core-textblock',
    props,
    require('./defaults.js').default
);
</script>
