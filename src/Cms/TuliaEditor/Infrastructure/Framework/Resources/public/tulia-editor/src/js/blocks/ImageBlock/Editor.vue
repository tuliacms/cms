<template>
    <div>
        <div :tued-contextmenu="contextmenu.register('block.image', block.id)">
            <ImageEditor
                @updated="$emit('updated')"
                v-model="block.data.image"
            ></ImageEditor>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);
const contextmenu = inject('contextmenu');

const ImageEditor = block.extension('Image');
</script>
