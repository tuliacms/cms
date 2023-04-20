<template>
    <i :class="iconClassname" @click="extension.execute('chose-icon')"></i>
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
const { defineProps, defineEmits, inject, onUnmounted, computed } = require('vue');
const props = defineProps(['modelValue', 'class']);
const extension = inject('extension.instance').editor('FontIcon');
const emit = defineEmits(['update:modelValue']);

const iconClassname = computed(() => {
    return `tued-ext-fonticon-element ${props.class} ${props.modelValue}`;
});

extension.operation('icon-chosen', (data, success, fail) => {
    emit('update:modelValue', data.icon);
    success();
});

onUnmounted(() => extension.unmount());
</script>
<script>
export default {name: 'Extension.Fonticon.Editor'}
</script>
