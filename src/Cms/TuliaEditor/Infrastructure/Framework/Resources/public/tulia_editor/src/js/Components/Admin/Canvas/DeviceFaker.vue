<template>
    <div class="tued-canvas-device-faker" :style="{ width: canvasOptions.size.breakpoint.width + 'px' }">
        <iframe :src="editorView"></iframe>
    </div>
</template>

<script>
export default {
    props: ['editorView', 'canvasOptions'],
    inject: ['eventDispatcher']
};
</script>
