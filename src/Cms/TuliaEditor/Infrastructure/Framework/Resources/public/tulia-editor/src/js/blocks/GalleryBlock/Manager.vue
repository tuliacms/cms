<template>
    <Select v-model="block.data.size" :label="translator.trans('imageSize')" :choices="sizesChoices"></Select>
    <Select v-model="block.data.columns" :label="translator.trans('columnsNumber')" :choices="columnsChoices"></Select>
    <Select v-model="block.data.marginBottom" :label="translator.trans('imagesBottomMargin')" :choices="marginChoices"></Select>
</template>

<script setup>
const { defineProps, inject, onMounted, computed, reactive, watch } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');
const options = inject('options');
const Select = block.control('Select');

/**
 * Modification options
 */
const sizesChoices = computed(() => {
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
const columnsChoices = {
    '2': '2 columns',
    '3': '3 columns',
    '4': '4 columns',
    '6': '6 columns',
};
const marginChoices = reactive({});

onMounted(() => {
    for (let i = 0; i <= options.elements.style.spacers.max; i++) {
        marginChoices[i] = i;
    }
});
</script>
