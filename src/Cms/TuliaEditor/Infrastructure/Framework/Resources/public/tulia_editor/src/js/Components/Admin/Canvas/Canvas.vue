<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner">
            <Sizer :canvas="canvas"></Sizer>
            <div @mousedown="messenger.send('editor.click.outside')">
                <DeviceFaker :editorView="editorView" :canvas="canvas"></DeviceFaker>
                <div class="body-coturn body-coturn-left" :style="{ transform: `translateX(-${coturn_position}px)` }"></div>
                <div class="body-coturn body-coturn-right" :style="{ transform: `translateX(${coturn_position}px)` }"></div>
            </div>
        </div>
    </div>
</template>

<script>
const Sizer = require('components/Admin/Canvas/Sizer.vue').default;
const DeviceFaker = require('components/Admin/Canvas/DeviceFaker.vue').default;

export default {
    props: ['editorView', 'canvas'],
    inject: ['eventDispatcher', 'selection', 'messenger'],
    components: {
        Sizer,
        DeviceFaker
    },
    computed: {
        coturn_position: function () {
            return this.canvas.size.breakpoint.width / 2;
        }
    }
};
</script>
