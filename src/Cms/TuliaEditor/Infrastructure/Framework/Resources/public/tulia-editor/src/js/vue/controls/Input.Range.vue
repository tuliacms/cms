<template>
    <div class="mb-3">
        <label class="form-label">{{ props.label }}</label>
        <input type="range" class="form-range" :min="min" :max="max" :step="step" v-model="model" />
    </div>
</template>

<script setup>
const { defineProps, defineEmits, inject, computed } = require('vue');
const props = defineProps(['modelValue', 'label', 'min', 'max', 'step']);
const emit = defineEmits(['update:modelValue']);
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>
