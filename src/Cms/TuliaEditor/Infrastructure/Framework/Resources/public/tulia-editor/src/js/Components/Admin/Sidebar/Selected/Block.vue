<template>
    <div>
        <Select v-model="data.margin.top[breakpointSize]" label="Margin top" :choices="choices"></Select>
        <Select v-model="data.margin.bottom[breakpointSize]" label="Margin bottom" :choices="choices"></Select>
        <Select v-model="data.padding.top[breakpointSize]" label="Padding top" :choices="choices"></Select>
        <Select v-model="data.padding.bottom[breakpointSize]" label="Padding bottom" :choices="choices"></Select>
    </div>
</template>
<script setup>
const { defineProps, inject, reactive, ref, watch, computed, toRaw, onMounted } = require('vue');
const props = defineProps(['block']);
const canvas = inject('canvas');
const block = inject('blocks.instance').manager(props);
const Select = block.control('Select');

const breakpointSize = computed (() => {
    return canvas.getBreakpointName();
});

const data = reactive({
    margin: {
        top: {xxl: '', xl: '', lg: '', md: '', sm: '', xs: ''},
        bottom: {xxl: '', xl: '', lg: '', md: '', sm: '', xs: ''},
    },
    padding: {
        top: {xxl: '', xl: '', lg: '', md: '', sm: '', xs: ''},
        bottom: {xxl: '', xl: '', lg: '', md: '', sm: '', xs: ''},
    },
});
const choices = {
    '': 'Default',
    0: 0,
    1: 1,
    2: 2,
    3: 3,
    4: 4,
    5: 5,
};

const deepCopyWithoutEmptyValues = (source) => {
    let result = {};

    for (let i in source) {
        if (typeof source[i] === 'object') {
            let copy = deepCopyWithoutEmptyValues(source[i]);

            if (Object.keys(copy).length) {
                result[i] = copy;
            }
        } else if (source[i] !== '' && source[i] !== null) {
            result[i] = source[i];
        }
    }

    return result;
};

const copyValuesFromBlock = (block, data) => {
    if (!block.data?._internal?.sizing) {
        return;
    }

    for (let a in block.data._internal.sizing) {
        for (let b in block.data._internal.sizing[a]) {
            for (let c in block.data._internal.sizing[a][b]) {
                if (block.data._internal.sizing[a][b][c]) {
                    data[a][b][c] = block.data._internal.sizing[a][b][c];
                }
            }
        }
    }
};

watch(data, async (newValue) => {
    if (!block.data._internal) {
        block.data._internal = {};
    }

    block.data._internal.sizing = deepCopyWithoutEmptyValues(toRaw(newValue));
});

onMounted(() => {
    copyValuesFromBlock(block, data);
});
</script>
