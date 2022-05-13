<template>
    <Select :choices="taxonomies" :label="props.label" v-model="model"></Select>
</template>

<script setup>
const Select = require('controls/Select.vue').default;
const { defineProps, defineEmits, computed } = require('vue');
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { require: true },
    label: { require: true },
});

const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});

const taxonomies = {
    '9a4c4ca3-4c83-46c0-8471-f2d5f8b9ef2c': 'Kategoria',
    'b89c2db5-2675-4268-8fef-aa30099ab871': 'Kolejna kategoria',
    '2e2f328e-72ff-48d2-b423-4936890f7d33': 'Kategoria podrzędna',
};
</script>
