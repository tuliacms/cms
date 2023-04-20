<template>
    <div v-if="props.modelValue.id">
        [image id="{{ props.modelValue.id }}" size="{{ props.size }}"]
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
                && value.hasOwnProperty('filename');
        }
    },
    size: {
        required: true,
    }
});
</script>
<script>
export default {name: 'Extension.Image.Render'}
</script>
