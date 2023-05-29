<template>
    <div>
        <Contenteditable v-model="block.data.content"></Contenteditable>
    </div>
</template>
<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('structure').block(props.block);

// Your block logic here...
const Contenteditable = block.extension('Contenteditable');
</script>
<script>
export default { name: 'TuliaLisaTheme.Block.{{ block.name }}.Editor' }
</script>
