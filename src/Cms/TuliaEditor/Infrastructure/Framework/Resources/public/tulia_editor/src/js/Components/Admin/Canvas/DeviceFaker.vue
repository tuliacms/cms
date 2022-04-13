<template>
    <div class="tued-canvas-device-faker" :style="{ width: canvas.size.breakpoint.width + 'px' }">
        <iframe :src="editorView"></iframe>
    </div>
</template>

<script>
export default {
    props: ['editorView', 'canvas'],
    inject: ['eventDispatcher'],
    mounted() {
        this.eventDispatcher.on('device.size.changed', (size) => {
            this.canvas.size.breakpoint.width = size.width;
            this.canvas.size.breakpoint.name = size.name;
        });
    }
};
</script>
