<template></template>
<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const translator = inject('translator');

// Your block logic here...
</script>
<script>
export default { name: 'TuliaLisaTheme.Block.{{ block.name }}.Manager' }
</script>
