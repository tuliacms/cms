<template></template>

<script setup>
const { computed, onMounted, defineProps, inject } = require('vue');


/****************
 * Prepare block
 ****************/
const props = defineProps(['id', 'data']);
const blocks = inject('blocks');
const block = blocks.manager(
    'core-textblock',
    props,
    require('./defaults.js').default
);



/**************
 * Block logic
 **************/
</script>
