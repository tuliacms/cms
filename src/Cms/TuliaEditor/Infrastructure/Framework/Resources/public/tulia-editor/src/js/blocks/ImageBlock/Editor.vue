<template>
    <div>
        <div :tued-contextmenu="contextmenu.register('block.image', block.id)">
            <ImageEditor
                @updated="$emit('updated')"
                v-model="block.data.image"
                :size="block.data.size"
                ref="image"
            ></ImageEditor>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, watch, ref } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);
const contextmenu = inject('contextmenu');
const ImageEditor = block.extension('Image');
const image = ref(null);

watch(() => block.data.size, async (newValue) => {
    image.value.changeSize(newValue);
});
</script>
