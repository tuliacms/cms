<template>
    Config: {{ block.config.someConfig }}<br />
    <input type="text" v-model="block.config.someConfig"><br />
    Data: {{ block.data.text }}
</template>

<script setup>
import { defineProps, inject } from "vue";

const props = defineProps(['block']);
const block = inject('instance.blocks').manager(props);
</script>
