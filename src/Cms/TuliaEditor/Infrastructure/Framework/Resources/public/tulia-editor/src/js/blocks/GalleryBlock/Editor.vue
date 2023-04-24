<template>
    <div>
        <div class="row">
            <div v-for="(image, i) in images.collection" :class="columnsClassname" :key="image.id">
                <Image v-model="images.collection[i].file" :size="block.config.size" :holder="block.id"></Image>
                <Actions actions="moveBackward,moveForward,remove" :collection="images" :item="image"></Actions>
            </div>
        </div>
        <div class="row">
            <div class="col-12 my-4 text-center">
                <Actions actions="add" :collection="images"></Actions>
            </div>
        </div>
    </div>
</template>

<script setup>
import { defineProps, inject, computed } from "vue";
import ColumnClassnameGenerator from "blocks/GalleryBlock/ColumnClassnameGenerator";

const props = defineProps(['block']);
const block = inject('instance.blocks').editor(props);
const extensions = inject('extensions.registry');
const Image = extensions.editor('Image');
const Collection = extensions.editor('Collection');
const Actions = extensions.editor('Collection.Actions');

const images = new Collection(block, 'images', {
    file: { id: null, filename: null }
});

const columnsClassname = computed(ColumnClassnameGenerator.computer(block));
</script>
<script>
export default { name: 'Block.Gallery.Editor' }
</script>
