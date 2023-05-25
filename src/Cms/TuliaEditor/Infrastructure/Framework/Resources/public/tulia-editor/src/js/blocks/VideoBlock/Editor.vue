<template>
    <div :class="classname" v-if="block.config.url">
        <img :src="VideoUrl.videoThumbnail(block.config.url)" />
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
export default { name: 'Block.Video.Editor' }
</script>
