<template>
    <div :class="classname" v-if="block.config.url">
        <iframe width="560" height="315" :src="VideoUrl.videoUrl(block.config.url)" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</template>

<script setup>
import { defineProps, inject, computed } from "vue";
const props = defineProps(['block']);
const block = inject('structure').block(props.block);
const VideoUrl = require('./VideoUrl.js').default;

const classname = computed(() => {
    return `ratio ratio-${block.config.ratio} mb-4`;
});
</script>
<script>
export default { name: 'Block.Video.Render' }
</script>
