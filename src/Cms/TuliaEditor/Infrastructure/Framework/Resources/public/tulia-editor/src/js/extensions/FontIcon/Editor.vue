<template>
    <span class="tued-ext-fonticon-element" @click="extension.execute('chose-icon')">
        <i :class="props.modelValue"></i>
    </span>
</template>

<style scoped lang="scss">
.tued-ext-fonticon-element {
    outline: none !important;

    &:hover {
        outline: 1px dotted #8c8c8c !important;
        cursor: pointer;
    }
}
</style>

<script setup>
const { defineProps, defineEmits, inject, onUnmounted } = require('vue');
const props = defineProps(['modelValue']);
const extension = inject('extension.instance').editor('FontIcon');
const emit = defineEmits(['update:modelValue']);

extension.operation('icon-chosen', (data, success, fail) => {
    emit('update:modelValue', data.icon);
    success();
});

onUnmounted(() => extension.unmount());
</script>

<script>
export default {
    name: 'FontIconEditor'
}
</script>
