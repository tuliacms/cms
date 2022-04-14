<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner">
            <Sizer :canvasOptions="canvasOptions"></Sizer>
            <div @mousedown="messenger.send('editor.click.outside')">
                <DeviceFaker :editorView="editorView" :canvasOptions="canvasOptions"></DeviceFaker>
                <div class="body-coturn body-coturn-left" :style="{ transform: `translateX(-${coturnPosition}px)` }"></div>
                <div class="body-coturn body-coturn-right" :style="{ transform: `translateX(${coturnPosition}px)` }"></div>
            </div>
        </div>
    </div>
</template>

<script>
const Sizer = require('components/Admin/Canvas/Sizer.vue').default;
const DeviceFaker = require('components/Admin/Canvas/DeviceFaker.vue').default;

export default {
    props: ['editorView', 'canvasOptions'],
    inject: ['messenger'],
    components: {
        Sizer,
        DeviceFaker
    },
    computed: {
        coturnPosition: function () {
            return this.canvasOptions.size.breakpoint.width / 2;
        }
    }
};
</script>
