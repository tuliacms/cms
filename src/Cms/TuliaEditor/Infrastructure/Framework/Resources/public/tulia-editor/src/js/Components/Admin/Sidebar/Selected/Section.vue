<template>
    <div>
        <Select
            v-model="section.data.containerWidth"
            :choices="containerWidthList"
            label="Container width"
        ></Select>
    </div>
</template>
<script setup>
const Select = require('controls/Select.vue').default;
const { defineProps, inject, reactive, watch } = require('vue');
const props = defineProps(['section']);
const section = inject('sections.instance').manager(props);

const containerWidthList = {
    'default': 'Default width',
    'full-width': 'Full width (fluid)',
    'full-width-no-padding': 'Full width (fluid) with no padding',
};
</script>
