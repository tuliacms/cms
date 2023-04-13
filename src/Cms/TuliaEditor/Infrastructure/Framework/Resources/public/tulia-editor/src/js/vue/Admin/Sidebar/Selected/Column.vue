<template>
    Column
<!--    <Alignment :column="props.column"></Alignment>-->
</template>
<script setup>
/*const Alignment = require('components/Admin/Sidebar/Selected/Parts/Column/Alignment.vue').default;
const { defineProps } = require('vue');
const props = defineProps(['column']);*/

import { defineProps, inject } from "vue";

const props = defineProps(['column']);
const column = inject('instance.columns').manager(props);
</script>
