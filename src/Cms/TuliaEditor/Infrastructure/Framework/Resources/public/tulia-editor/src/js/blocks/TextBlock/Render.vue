<template>
    <div v-html="block.data.text"></div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
</script>
<script>
export default { name: 'Block.Text.Render' }
</script>
