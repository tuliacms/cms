<template>
    <div class="row">
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

const columnsClassname = computed(ColumnClassnameGenerator.computer(block));
</script>
