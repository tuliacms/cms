<template>
    <Select v-model="block.config.size" :label="translator.trans('imageSize')" :choices="sizesChoices"></Select>
    <Select v-model="block.config.columns" :label="translator.trans('columnsNumber')" :choices="columnsChoices"></Select>
    <Select v-model="block.config.marginBottom" :label="translator.trans('imagesBottomMargin')" :choices="marginChoices"></Select>
    <Switch v-model="block.config.onclickGallery" :label="translator.trans('onclickGallery')"></Switch>
</template>

<script setup>
const { defineProps, inject, onMounted, onUnmounted, computed, reactive, watch } = require('vue');
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const controls = inject('controls.registry');
const translator = inject('translator');
const options = inject('options');
const assets = inject('assets');
const Select = controls.manager('Select');
const Switch = controls.manager('Switch.YesNo');

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

watch(() => block.config.onclickGallery, async (newValue) => {
    if (newValue === '1') {
        assets.require(block.id, 'magnific_popup');
    } else {
        assets.remove(block.id, 'magnific_popup');
    }
});

onMounted(() => {
    for (let i = 0; i <= options.elements.style.spacers.max; i++) {
        marginChoices[i] = i;
    }

    assets.require(block.id, 'magnific_popup');
});

onUnmounted(() => {
    assets.remove(block.id, 'magnific_popup');
});
</script>
<script>
export default { name: 'Block.Gallery.Manager' }
</script>
