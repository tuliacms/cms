<template>
    <Select v-model="block.data.image.size" :label="translator.trans('imageSize')" :choices="choices"></Select>
</template>

<script setup>
const { defineProps, inject, onMounted, computed } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');
const options = inject('options');

const Select = block.control('Select');

const choices = computed(() => {
    let choices = {original: 'Original'};

    for (let i in options.filemanager.image_sizes) {
        let size = options.filemanager.image_sizes[i];

        choices[size.name] = `${size.label} - `;

        if (size.mode === 'widen') {
            choices[size.name] += `widen ${size.width}px`;
        } else {
            choices[size.name] += `${size.width}x${size.height}px`;
        }
    }

    return choices;
});
</script>
