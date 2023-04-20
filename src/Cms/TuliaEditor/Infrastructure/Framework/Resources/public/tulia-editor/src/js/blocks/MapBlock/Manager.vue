<template>
    <Text v-model="block.config.height" :label="translator.trans('mapHeight')"></Text>
    <Range v-model="block.config.zoom" :label="translator.trans('mapZoom')" min="4" max="19" step="1"></Range>
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('instance.blocks').manager(props);
const translator = inject('translator');
const controls = inject('controls.registry');

const Text = controls.manager('Input.Text');
const Range = controls.manager('Input.Range');

block.receive('map.zoom.change', zoom => {
    block.config.zoom = zoom;
});
</script>
<script>
export default { name: 'Block.Map.Manager' }
</script>
