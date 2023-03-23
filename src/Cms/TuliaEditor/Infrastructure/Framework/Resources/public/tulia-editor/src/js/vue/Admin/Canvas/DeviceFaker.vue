<template>
    <div class="tued-canvas-device-faker" :style="{ width: canvas.currentBreakpoint.width + 'px' }">
        <iframe :src="editorView" ref="iframe"></iframe>
    </div>
</template>

<script setup>
import { inject, defineProps, onMounted, ref } from "vue";
const props = defineProps(['editorView']);
const canvas = inject('canvas');
const messenger = inject('messenger');
const iframe = ref(null);

onMounted(() => {
    messenger.setDestinationWindow(iframe.value.contentWindow);
});
</script>
