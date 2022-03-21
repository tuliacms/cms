<template>
    <div class="tued-container" ref="root">
        <Canvas
            :editorView="`${options.editor.view}?tuliaEditorInstance=${this.instanceId}`"
        ></Canvas>
        <Sidebar :availableBlocks="availableBlocks"></Sidebar>
    </div>
</template>

<script>
import Canvas from './../Components/Canvas/Canvas.vue';
import Sidebar from './../Components/Sidebar/Sidebar.vue';

export default {
    props: ['options', 'instanceId', 'messanger', 'availableBlocks'],
    components: {
        Canvas,
        Sidebar
    },
};
</script>
