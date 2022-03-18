<template>
    <div class="tued-container" ref="root">
        <Canvas :structure="options.structure.source" :availableBlocks="availableBlocks"></Canvas>
    </div>
</template>

<script>
import Canvas from './Components/Canvas/Canvas.vue';

export default {
    props: ['options', 'instanceId', 'messanger', 'availableBlocks'],
    components: {
        Canvas
    },
    methods: {
        edit: function () {
            this.options.layout.viewMode = 'editor';
        },
        save: function () {
            this.options.layout.viewMode = 'preview';
        },
        cancel: function () {
            this.options.layout.viewMode = 'preview';
        }
    }
};
</script>
