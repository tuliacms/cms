<template>
    <DesignOptions :block="block"></DesignOptions>
    <Visibility :block="block"></Visibility>
</template>
<script setup>
import Visibility from "admin/Sidebar/Selected/Parts/Block/Visibility.vue";
import DesignOptions from "admin/Sidebar/Selected/Parts/Block/DesignOptions.vue";
import { defineProps, inject } from "vue";

const props = defineProps(['block']);
const block = inject('structure').block(props.block);
</script>
