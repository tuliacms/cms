<template>
<!--    <DesignOptions :block="props.block"></DesignOptions>-->
    <Visibility :block="block"></Visibility>
</template>
<script setup>
/*const DesignOptions = require('components/Admin/Sidebar/Selected/Parts/Block/DesignOptions.vue').default;*/
import Visibility from "admin/Sidebar/Selected/Parts/Block/Visibility.vue";
import { defineProps, inject } from "vue";

const props = defineProps(['block']);
const block = inject('instance.blocks').manager(props);
</script>
