<template>
    <div class="row tulia-popup-gallery" v-if="block.config.onclickGallery === '1'">
        <div v-for="image in block.data.images" :class="columnsClassname" :key="image.id">
            <a :href="filemanager.generatePreviewImagePath(image.file, 'original')">
                <Image v-model="image.file" :size="block.config.size"></Image>
            </a>
        </div>
    </div>
    <div class="row" v-else>
        <div v-for="image in block.data.images" :class="columnsClassname" :key="image.id">
            <Image v-model="image.file" :size="block.config.size"></Image>
        </div>
    </div>
</template>

<script setup>
import { defineProps, inject, computed } from "vue";
const ColumnClassnameGenerator = require('./ColumnClassnameGenerator.js').default;
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const extensions = inject('extensions.registry');
const Image = extensions.render('Image');
const filemanager = extensions.render('Filemanager');

const columnsClassname = computed(ColumnClassnameGenerator.computer(block));
</script>
<script>
export default { name: 'Block.Gallery.Render' }
</script>
