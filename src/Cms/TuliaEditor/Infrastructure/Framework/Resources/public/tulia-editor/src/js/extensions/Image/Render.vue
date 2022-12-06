<template>
    <div v-if="props.modelValue.id">
        [image id="{{ props.modelValue.id }}" size="{{ props.modelValue.size }}"]
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps({
    modelValue: {
        required: true,
        type: Object,
        validator (value) {
            return value.hasOwnProperty('id')
                && value.hasOwnProperty('filename')
                && value.hasOwnProperty('size');
        }
    }
});
</script>
