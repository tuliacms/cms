<template>
    <div v-html="block.data.text"></div>
</template>

<script setup>
const { defineProps, inject } = require('vue');

/****************
 * Prepare block
 ****************/
const props = defineProps(require('./props.js').default);
const blocks = inject('blocks');
const block = blocks.render('core-textblock', props);
</script>
