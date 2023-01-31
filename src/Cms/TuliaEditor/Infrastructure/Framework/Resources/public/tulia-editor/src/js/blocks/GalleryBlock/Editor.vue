<template>
    <div>
        <div class="row">
            <div v-for="(image, i) in images.collection" :class="columnsClassname" :key="image.id">
                <Image v-model="images.collection[i].file" :size="block.data.size" @updated="$emit('updated')" :ref="setRef"></Image>
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
const { defineProps, inject, computed, reactive, onMounted, watch, ref, toRaw } = require('vue');
const ColumnClassnameGenerator = require('./ColumnClassnameGenerator.js').default;
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);
const view = inject('canvas.view');
const Image = block.extension('Image');
const Collection = block.extension('Collection');
const Actions = block.extension('Collection.Actions');

const imageExtList = ref([]);
function setRef(v){
    if (!imageExtList.value.includes(v)) {
        imageExtList.value.push(v);
    }
}

const images = new Collection(block.data.images, {
    file: { id: null, filename: null }
});

const columnsClassname = computed(ColumnClassnameGenerator.computer(block));

watch(() => block.data.size, async (newValue) => {
    for (let i in imageExtList.value) {
        imageExtList.value[i].changeSize(newValue);
    }
});
</script>
<script>
export default {
    name: 'GalleryEditor'
}
</script>
