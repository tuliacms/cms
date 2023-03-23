<template>
    <div class="tued-canvas-device-faker" :style="{ width: canvas.currentBreakpoint.size + 'px' }">
        <iframe :src="editorView" @load="loaded"></iframe>
    </div>
</template>

<script setup>
import { inject, defineProps } from "vue";
const props = defineProps(['editorView']);
const canvas = inject('canvas');
const eventBus = inject('eventBus');

const loaded = () => {
    console.log('iframe');
    /*this.messenger.addWindow(this.$refs['canvas-faker-iframe'].contentWindow);
    this.messenger.notify('editor.ready');*/
};
</script>
