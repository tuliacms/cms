<template>
    <p>{{ block.data.content }}</p>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);

// Your block logic here...
</script>
