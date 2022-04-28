<template></template>

<script setup>
/****************
 * Prepare block
 ****************/
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const blocks = inject('blocks');
const block = blocks.manager(props);

/**************
 * Block logic
 **************/
</script>
