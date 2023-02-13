<template>
    <DesignOptions :block="props.block"></DesignOptions>
    <Visibility :block="props.block"></Visibility>
</template>
<script setup>
const DesignOptions = require('components/Admin/Sidebar/Selected/Parts/Block/DesignOptions.vue').default;
const Visibility = require('components/Admin/Sidebar/Selected/Parts/Block/Visibility.vue').default;
const { defineProps } = require('vue');
const props = defineProps(['block']);
</script>
