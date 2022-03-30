<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner">
            <Sizer :canvas="canvas"></Sizer>
            <div @mousedown="$root.$emit('structure.selection.outsite')">
                <DeviceFaker :editorView="editorView" :canvas="canvas"></DeviceFaker>
                <div class="body-coturn body-coturn-left" :style="{ transform: `translateX(-${coturn_position}px)` }"></div>
                <div class="body-coturn body-coturn-right" :style="{ transform: `translateX(${coturn_position}px)` }"></div>
            </div>
        </div>
    </div>
</template>

<script>
import Sizer from './Sizer.vue';
import DeviceFaker from './DeviceFaker.vue';

export default {
    props: ['editorView', 'canvas'],
    components: {
        Sizer,
        DeviceFaker
    },
    computed: {
        coturn_position: function () {
            this.coturn_position = this.canvas.size.breakpoint.width / 2;
        }
    }
};
</script>
