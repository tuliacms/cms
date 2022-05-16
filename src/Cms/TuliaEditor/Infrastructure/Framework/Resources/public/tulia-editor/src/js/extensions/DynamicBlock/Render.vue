<template>
    <div class="tued-dynamic-block">[dynamic_block {{ attributes }}]</div>
</template>

<script setup>
const { defineProps, defineEmits, inject, onMounted, isReactive, reactive, ref, computed, toRaw, watch } = require('vue');
const props = defineProps(['type', 'data']);

const attributes = ref(props.data);

const getReactiveData = (source) => {
    if (source.hasOwnProperty('reactiveData')) {
        return source.reactiveData;
    }
    if (isReactive(source)) {
        return source;
    }

    throw new Error('The "data" property must be Block data or reactive object to work properly.');
};

const serializeAttributes = (source, prefix) => {
    let result = '';

    if (!prefix) {
        prefix = '';
    }

    for (let i in source) {
        if (typeof source[i] === 'object' || Array.isArray(source[i])) {
            result += serializeAttributes(source[i], i + '_');
        } else {
            result += ` ${prefix}${i}="${source[i]}"`;
        }
    }

    return result;
};

const updateAttributes = (data) => {
    attributes.value = `type="${props.type}"` + serializeAttributes(toRaw(data));
};

onMounted(() => {
    const data = getReactiveData(props.data);

    updateAttributes(data);
    watch(data, (newData) => updateAttributes(newData));
});
</script>

<script>
export default {
    name: 'DynamicBlockRender'
}
</script>
