<template>
    <Image v-model="block.data.image"></Image>
</template>

<script setup>
/****************
 * Prepare block
 ****************/
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
const Image = block.extension('Image');
</script>
