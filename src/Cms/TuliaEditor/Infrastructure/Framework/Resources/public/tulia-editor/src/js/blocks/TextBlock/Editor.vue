<template>
    <div><WysiwygEditor v-model="block.data.text"></WysiwygEditor></div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks').editor(props);

const WysiwygEditor = block.extension('WysiwygEditor');
</script>
