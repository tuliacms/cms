<template>
    <ImageEditor
        @updated="$emit('updated')"
        :block="block"
        v-model="block.data.image"
    ></ImageEditor>
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
const ImageEditor = block.extension('Image');
</script>
