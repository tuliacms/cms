<template>
    <div class="mb-3">
        <label v-if="props.label" class="form-label">{{ props.label }}</label>
        <input class="form-control" v-model="model" :type="type" />
        <div v-if="props.help" class="form-text">{{ props.help }}</div>
    </div>
</template>

<script setup>
const { defineProps, defineEmits, inject, computed } = require('vue');
const props = defineProps(['modelValue', 'label', 'type', 'help']);
const emit = defineEmits(['update:modelValue']);
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
const type = props.type ?? 'text';
</script>
