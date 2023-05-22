<template>
    <div class="tued-dynamic-block">[dynamic_block {{ attributes }}]</div>
</template>
<script setup>
const { defineProps, defineEmits, inject, onMounted, reactive, ref, computed, watch } = require('vue');
const props = defineProps(['type', 'data']);

const attributes = ref(props.data);

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
    attributes.value = `type="${props.type}"` + serializeAttributes(data);
};

onMounted(() => {
    updateAttributes(props.data.export);
    watch(() => props.data, (newData) => updateAttributes(newData));
});
</script>
<script>
export default {name: 'Extension.DynamicBlock.Editor'}
</script>
