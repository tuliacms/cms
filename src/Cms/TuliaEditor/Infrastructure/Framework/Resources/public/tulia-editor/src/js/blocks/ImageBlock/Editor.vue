<template>
    <ImageEditor
        @updated="$emit('updated')"
        v-model="block.data.image"
    ></ImageEditor>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks').editor(props);

const ImageEditor = block.extension('Image');
</script>
