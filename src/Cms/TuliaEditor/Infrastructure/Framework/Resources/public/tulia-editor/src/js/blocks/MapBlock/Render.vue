<template>
    <div>[map height="{{ block.data.height }}" lat="{{ block.data.position.lat }}" lng="{{ block.data.position.lng }}" zoom="{{ block.data.zoom }}"]</div>
</template>

<script setup>
const { defineProps, inject, onMounted } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
</script>
