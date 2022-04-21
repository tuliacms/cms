<template></template>

<script setup>
const { computed, onMounted, defineProps, inject } = require('vue');


/****************
 * Prepare block
 ****************/
const props = defineProps(require('./props.js').default);
const blocks = inject('blocks');
const block = blocks.manager('core-textblock', props);



/**************
 * Block logic
 **************/
</script>
