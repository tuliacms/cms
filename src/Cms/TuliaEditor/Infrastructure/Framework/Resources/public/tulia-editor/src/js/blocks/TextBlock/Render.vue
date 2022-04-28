<template>
    <div v-html="block.data.text"></div>
</template>

<script setup>
/****************
 * Prepare block
 ****************/
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const blocks = inject('blocks');
const block = blocks.render(props);

/**************
 * Block logic
 **************/
</script>
