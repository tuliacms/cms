<template>
    <div class="mb-3">
        <label class="form-label">{{ props.label }}</label>
        <div v-for="(label, value) in props.choices" :key="value">
            <label><input type="radio" :value="value" v-model="model" /> {{ label }}</label>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, defineEmits, ref, watch, computed } = require('vue');
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    multiple: { require: false, default: false },
    choices: { require: true },
    modelValue: { require: true },
    label: { require: true },
});

const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>
