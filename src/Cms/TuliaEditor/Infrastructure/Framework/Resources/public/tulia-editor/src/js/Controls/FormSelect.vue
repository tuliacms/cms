<template>
    <Select :choices="forms" :label="props.label" v-model="model"></Select>
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

const forms = {
    'b8c00685-9e62-4b60-9368-e8bfbc82220a': 'Contact form',
};
</script>
