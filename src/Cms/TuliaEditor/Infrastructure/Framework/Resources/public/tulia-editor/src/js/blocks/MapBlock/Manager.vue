<template>
    <Text v-model="block.data.height" :label="translator.trans('mapHeight')"></Text>
<!--    <Range v-model="block.data.zoom" :label="translator.trans('mapZoom')" min="4" max="19" step="1"></Range>-->
</template>

<script setup>
const { defineProps, inject, onMounted } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');

const Text = block.control('Input.Text');
const Range = block.control('Input.Range');
</script>
