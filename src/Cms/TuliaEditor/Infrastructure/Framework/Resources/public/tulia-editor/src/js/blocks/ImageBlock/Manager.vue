<template>
    <Select v-model="block.config.size" :label="translator.trans('imageSize')" :choices="choices"></Select>
</template>

<script setup>
import { defineProps, inject, computed } from "vue";
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const Select = inject('controls.registry').manager('Select');
const translator = inject('translator');
const options = inject('options');

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
<script>
export default { name: 'Block.Image.Manager' }
</script>
