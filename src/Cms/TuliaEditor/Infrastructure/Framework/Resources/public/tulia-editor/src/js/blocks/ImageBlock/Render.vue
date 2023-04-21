<template>
    <Image v-model="block.data.image" :size="block.config.size"></Image>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('instance.blocks').render(props);
const Image = inject('extensions.registry').render('Image');
</script>
<script>
export default { name: 'Block.Image.Render' }
</script>
