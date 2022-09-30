<template>
    <div :class="classname" v-if="block.data.url">
        <img :src="VideoUrl.videoThumbnail(block.data.url)" />
    </div>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);
const VideoUrl = require('./VideoUrl.js').default;

const classname = computed(() => {
    return `ratio ratio-${block.data.ratio} mb-4`;
});
</script>
