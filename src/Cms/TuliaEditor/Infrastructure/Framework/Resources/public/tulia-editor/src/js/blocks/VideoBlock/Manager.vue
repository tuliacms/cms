<template>
    <Text v-model="block.data.url" :label="translator.trans('youtubeVideoUrl')" />
    <Select v-model="block.data.ratio" :label="translator.trans('aspectRatio')" :choices="choices" />
</template>

<script setup>
const { defineProps, inject, onMounted } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');
const Select = block.control('Select');
const Text = block.control('Input.Text');

const choices = {
    '1x1': '1x1',
    '4x3': '4x3',
    '16x9': '16x9',
    '21x9': '21x9',
};
</script>
