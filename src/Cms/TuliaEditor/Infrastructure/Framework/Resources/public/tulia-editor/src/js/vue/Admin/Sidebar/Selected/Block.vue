<template>
    Block
<!--    <DesignOptions :block="props.block"></DesignOptions>
    <Visibility :block="props.block"></Visibility>-->
</template>
<script setup>
/*const DesignOptions = require('components/Admin/Sidebar/Selected/Parts/Block/DesignOptions.vue').default;
const Visibility = require('components/Admin/Sidebar/Selected/Parts/Block/Visibility.vue').default;*/
import { defineProps, inject } from "vue";

const props = defineProps(['block']);
const block = inject('instance.blocks').manager(props);
</script>
