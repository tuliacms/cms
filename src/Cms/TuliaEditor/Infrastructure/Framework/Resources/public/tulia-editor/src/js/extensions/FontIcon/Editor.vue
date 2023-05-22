<template>
    <i :class="iconClassname" @click="extension.send('chose-icon')"></i>
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
const props = defineProps(['modelValue', 'class', 'instanceId']);
const extension = inject('instance.extensions').editor('FontIcon', props.instanceId);
const emit = defineEmits(['update:modelValue']);

const iconClassname = computed(() => {
    return `tued-ext-fonticon-element ${props.class} ${props.modelValue}`;
});

extension.receive('icon-chosen', (data) => {
    emit('update:modelValue', data.icon);
});

onUnmounted(() => extension.unmount());
</script>
<script>
export default {name: 'Extension.Fonticon.Editor'}
</script>
