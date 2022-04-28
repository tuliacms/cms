<template>
    <div><WysiwygEditor v-model="block.data.text" :blockId="block.id"></WysiwygEditor></div>
</template>

<script setup>
/****************
 * Prepare block
 ****************/
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const blocks = inject('blocks');
const block = blocks.editor(props);

/**************
 * Block logic
 **************/
const WysiwygEditor = require('extensions/WysiwygEditor.vue').default;
</script>
