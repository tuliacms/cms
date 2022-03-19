<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner">
            <Sizer></Sizer>
            <Device :structure="structure"></Device>
        </div>
    </div>
</template>

<script>
import Sizer from './Sizer.vue';
import Device from './Device.vue';

export default {
    props: ['structure',],
    components: {
        Sizer,
        Device
    }
};
</script>
