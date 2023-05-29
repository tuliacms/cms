<template>
    <div>[map height="{{ block.config.height }}" lat="{{ block.data.position.lat }}" lng="{{ block.data.position.lng }}" zoom="{{ block.config.zoom }}"]</div>
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
</script>
<script>
export default { name: 'Block.Map.Render' }
</script>
