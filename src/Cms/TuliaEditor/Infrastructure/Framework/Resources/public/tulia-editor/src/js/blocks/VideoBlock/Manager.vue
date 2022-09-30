<template>
    <Select v-model="block.data.ratio" label="Aspect Ratio" :choices="choices" />
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);

const Select = block.control('Select');

const choices = {
    '1x1': '1x1',
    '4x3': '4x3',
    '16x9': '16x9',
    '21x9': '21x9',
};
</script>
