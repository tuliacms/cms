<template>
    <div class="mb-3">
        <label class="form-label">{{ props.label }}</label>
        <input type="text" class="form-control" v-model="model" />
    </div>
</template>

<script setup>
const { defineProps, defineEmits, inject, computed } = require('vue');
const props = defineProps(['modelValue', 'label']);
const emit = defineEmits(['update:modelValue']);
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>
