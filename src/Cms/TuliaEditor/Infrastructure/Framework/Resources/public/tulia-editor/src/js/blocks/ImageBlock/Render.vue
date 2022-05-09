<template>
    <div>[image id="{{ block.data.image.id }}" size="thumbnail"]</div>
</template>

<script setup>
/****************
 * Prepare block
 ****************/
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
</script>
