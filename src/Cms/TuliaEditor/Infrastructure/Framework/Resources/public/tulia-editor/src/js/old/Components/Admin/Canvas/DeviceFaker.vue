<template>
    <div class="tued-canvas-device-faker" :style="{ width: canvasOptions.size.breakpoint.width + 'px' }">
        <iframe :src="editorView" ref="canvas-faker-iframe"></iframe>
    </div>
</template>

<script>
export default {
    props: ['editorView', 'canvasOptions'],
    inject: ['messenger'],
    mounted () {
        this.$refs['canvas-faker-iframe'].onload = () => {
            this.messenger.addWindow(this.$refs['canvas-faker-iframe'].contentWindow);
            this.messenger.notify('editor.ready');
        }
    }
};
</script>
