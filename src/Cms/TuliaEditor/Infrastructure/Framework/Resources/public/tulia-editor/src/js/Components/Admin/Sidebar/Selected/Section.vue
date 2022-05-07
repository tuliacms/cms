<template>
    <div>
        <Select
            v-model="containerWidth"
            :choices="containerWidthList"
            label="Container width"
        ></Select>
    </div>
</template>
<script setup>
const Select = require('controls/Select.vue').default;
const { defineProps, inject, ref, watch } = require('vue');
const props = defineProps(['section']);
const section = inject('sections').manager(props);


/******************
 * Container width
 ******************/
const containerWidthList = {
    'default': 'Default width',
    'full-width': 'Full width (fluid)',
    'full-width-no-padding': 'Full width (fluid) with no padding',
};
const containerWidth = ref(section.data.containerWidth);

watch(containerWidth, async () => {
    section.data.containerWidth = containerWidth.value;
    let value;

    if (containerWidth.value === 'full-width-no-padding') {
        value = 'no-gutters';
    } else {
        value = 'default';
    }

    for (let i in section.children) {
        section.children[i].data.gutters = value;
    }
});
</script>
