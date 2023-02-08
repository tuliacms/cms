<template>
    <div class="row tulia-popup-gallery" v-if="block.data.onclickGallery === '1'">
        <div v-for="image in block.data.images" :class="columnsClassname" :key="image.id">
            <a :href="filemanager.generatePreviewImagePath(image.file, 'original')">
                <Image v-model="image.file" :size="block.data.size"></Image>
            </a>
        </div>
    </div>
    <div class="row" v-else>
        <div v-for="image in block.data.images" :class="columnsClassname" :key="image.id">
            <Image v-model="image.file" :size="block.data.size"></Image>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');
const ColumnClassnameGenerator = require('./ColumnClassnameGenerator.js').default;
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
const Image = block.extension('Image');
const filemanager = inject('filemanager');

const columnsClassname = computed(ColumnClassnameGenerator.computer(block));
</script>
