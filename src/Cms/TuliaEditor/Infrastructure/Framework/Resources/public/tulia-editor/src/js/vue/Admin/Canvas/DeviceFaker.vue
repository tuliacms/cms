<template>
    <div class="tued-canvas-device-faker" :style="{ width: canvas.currentBreakpoint.width + 'px' }">
        <iframe :src="editorView" ref="iframe"></iframe>
    </div>
</template>

<script setup>
import { inject, onMounted, ref } from "vue";
const canvas = inject('canvas');
const messenger = inject('messenger');
const options = inject('options');
const instanceId = inject('instanceId');
const iframe = ref(null);

const editorView = options.editor.view + '?tuliaEditorInstance=' + instanceId;

onMounted(() => {
    messenger.setDestinationWindow(iframe.value.contentWindow);
});
</script>
