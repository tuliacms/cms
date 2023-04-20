<template>
    <Text v-model="block.config.url" :label="translator.trans('youtubeVideoUrl')" />
    <Select v-model="block.config.ratio" :label="translator.trans('aspectRatio')" :choices="choices" />
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('instance.blocks').manager(props);
const translator = inject('translator');
const controls = inject('controls.registry');
const Select = controls.manager('Select');
const Text = controls.manager('Input.Text');

const choices = {
    '1x1': '1x1',
    '4x3': '4x3',
    '16x9': '16x9',
    '21x9': '21x9',
};
</script>
<script>
export default { name: 'Block.Video.Manager' }
</script>
