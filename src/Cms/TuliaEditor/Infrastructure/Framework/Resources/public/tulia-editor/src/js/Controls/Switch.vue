<template>
    <div class="mb-3">
        <label v-if="props.label" class="form-label">{{ props.label }}</label>
        <div class="btn-group d-flex" role="group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn btn-outline-success flex-fill" for="btnradio1">Radio 1</label>
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn btn-outline-danger flex-fill" for="btnradio2">Radio 2</label>
        </div>
        <div v-if="props.help" class="form-text">{{ props.help }}</div>
    </div>
</template>
<script setup>
const { defineProps, defineEmits, inject, computed } = require('vue');
const _ = require('lodash');
const props = defineProps(['modelValue', 'label', 'help']);
const emit = defineEmits(['update:modelValue']);
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
</script>
