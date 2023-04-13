<template>
    Config: {{ block.config.someConfig }}
    <input type="text" v-model="block.config.someConfig">
</template>

<script setup>
import { defineProps, inject } from "vue";

const props = defineProps(['block']);
const block = inject('instance.blocks').manager(props);
</script>
