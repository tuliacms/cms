<template>
    <div class="tued-canvas-device-faker" :style="{ width: this.width }">
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
            width: '100%'
        }
    },
    methods: {
        changeSizeTo: function (size) {
            this.width = size.width + 'px';
        },
    },
    mounted() {
        this.$root.$on('device.size.changed', (size) => {
            this.changeSizeTo(size);
        });
    }
};
</script>
