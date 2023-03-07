<template>
    <div>
        <Contenteditable v-model="block.data.content"></Contenteditable>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);

// Your block logic here...
const Contenteditable = block.extension('Contenteditable');
</script>
