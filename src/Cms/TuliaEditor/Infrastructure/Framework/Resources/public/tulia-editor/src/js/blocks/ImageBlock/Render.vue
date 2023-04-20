<template>
    <Image v-model="block.data.image" :size="block.data.size"></Image>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
const Image = block.extension('Image');
</script>
<script>
export default { name: 'Block.Image.Render' }
</script>
