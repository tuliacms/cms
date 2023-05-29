<template>
    <div :tued-contextmenu="contextmenu.register('block.image', block.id)">
        <ImageEditor
            @updated="$emit('updated')"
            v-model="block.data.image"
            :size="block.config.size"
            :holder="block.id"
        ></ImageEditor>
    </div>
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const ImageEditor = inject('extensions.registry').editor('Image');
const contextmenu = inject('contextmenu');
</script>
<script>
export default { name: 'Block.Image.Image' }
</script>
