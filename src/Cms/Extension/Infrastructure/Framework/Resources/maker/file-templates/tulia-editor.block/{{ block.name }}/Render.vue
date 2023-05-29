<template>
    <p>{{ block.data.content }}</p>
</template>
<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('structure').block(props.block);

// Your block logic here...
</script>
<script>
export default { name: 'TuliaLisaTheme.Block.{{ block.name }}.Render' }
</script>
