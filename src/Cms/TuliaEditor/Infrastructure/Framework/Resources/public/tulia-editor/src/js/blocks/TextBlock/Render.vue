<template>
    <div v-html="block.data.text"></div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
</script>
