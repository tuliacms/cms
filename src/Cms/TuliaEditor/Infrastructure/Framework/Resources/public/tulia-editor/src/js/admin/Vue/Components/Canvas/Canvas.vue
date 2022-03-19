<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner" @mousedown.self="$root.$emit('structure.selectable.outsite')">
            <Sizer></Sizer>
            <DeviceFaker :structure="structure"></DeviceFaker>
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
    }
};
</script>
