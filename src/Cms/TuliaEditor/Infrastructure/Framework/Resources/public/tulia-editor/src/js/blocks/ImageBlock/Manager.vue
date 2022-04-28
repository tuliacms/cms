<template>
    <div class="mb-3">
        <label class="form-label">Image source</label>
        <ImageManager
            :block="block"
            v-model="block.data.image"
        ></ImageManager>
    </div>
</template>

<script setup>
/****************
 * Prepare block
 ****************/
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const blocks = inject('blocks');
const block = blocks.manager(props);

/**************
 * Block logic
 **************/
const ImageManager = require('extensions/Image/Image.js').default.Manager;
</script>
