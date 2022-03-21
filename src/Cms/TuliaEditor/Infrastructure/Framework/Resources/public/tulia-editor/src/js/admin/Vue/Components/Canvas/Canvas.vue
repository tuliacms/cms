<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner" @mousedown.self="$root.$emit('structure.selectable.outsite')">
            <Sizer></Sizer>
            <DeviceFaker :structure="structure"></DeviceFaker>
            <div class="body-coturn body-coturn-left" :style="{ transform: `translateX(-${coturn_position}px)` }"></div>
            <div class="body-coturn body-coturn-right" :style="{ transform: `translateX(${coturn_position}px)` }"></div>
        </div>
    </div>
</template>

<script>
import Sizer from './Sizer.vue';
import DeviceFaker from './DeviceFaker.vue';

export default {
    props: ['structure'],
    components: {
        Sizer,
        DeviceFaker
    },
    data () {
        return {
            coturn_position: 700
        };
    },
    mounted () {
        this.$root.$on('device.size.changed', (size) => {
            this.coturn_position = size.width / 2;
        });
    }
};
</script>
