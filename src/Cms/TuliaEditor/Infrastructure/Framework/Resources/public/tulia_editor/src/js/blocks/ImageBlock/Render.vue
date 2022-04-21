<template>
    <div>[image id="{{ block.data.imageId }}" size="thumbnail"]</div>
</template>

<script setup>
const { defineProps, inject } = require('vue');

/****************
 * Prepare block
 ****************/
const props = defineProps(require('./props.js').default);
const blocks = inject('blocks');
const block = blocks.render('core-imageblock', props);

</script>
