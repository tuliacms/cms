<template>
    <div>[map height="{{ block.config.height }}" lat="{{ block.data.position.lat }}" lng="{{ block.data.position.lng }}" zoom="{{ block.config.zoom }}"]</div>
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('instance.blocks').render(props);
</script>
<script>
export default { name: 'Block.Map.Render' }
</script>
