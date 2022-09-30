<template>
    <div :class="classname" v-if="block.data.url">
        <iframe width="560" height="315" :src="VideoUrl.videoUrl(block.data.url)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
const VideoUrl = require('./VideoUrl.js').default;

const classname = computed(() => {
    return `ratio ratio-${block.data.ratio} mb-4`;
});
</script>
