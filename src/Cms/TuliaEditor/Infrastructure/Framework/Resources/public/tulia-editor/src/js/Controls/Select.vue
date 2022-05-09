<template>
    <div class="mb-3">
        <label class="form-label">{{ props.label }}</label>
        <div v-for="(label, value) in props.choices" :key="value">
            <label><input type="radio" :value="value" v-model="model" /> {{ label }}</label>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, defineEmits, ref, watch } = require('vue');
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    multiple: { require: false, default: false },
    choices: { require: true },
    modelValue: { require: true },
    label: { require: true },
});
const model = ref(props.modelValue);

watch(model, async (newValue, oldValue) => {
    emit('update:modelValue', newValue);
});
</script>
