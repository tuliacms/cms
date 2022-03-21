<template>
    <div class="tued-canvas-device-faker" :style="{ width: this.width + 'px' }">
        <Structure :structure="structure"></Structure>
    </div>
</template>

<script>
import Structure from  '../Structure/Structure.vue';

export default {
    props: ['structure'],
    components: {
        Structure
    },
    data () {
        return {
            width: 1400
        }
    },
    methods: {
        changeSizeTo: function (size) {
            this.width = size.width;
        },
    },
    mounted() {
        this.$root.$on('device.size.changed', (size) => {
            this.changeSizeTo(size);
        });
    }
};
</script>
