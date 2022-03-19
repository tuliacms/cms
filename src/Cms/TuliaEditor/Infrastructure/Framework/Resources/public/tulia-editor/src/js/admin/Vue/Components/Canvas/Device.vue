<template>
    <div class="tued-canvas-device-faker" :style="{ width: this.width }">
        <div class="tued-element-boundaries tued-element-selected-boundaries tued-hidden">
            <div class="tued-node-name"></div>
        </div>
        <div class="tued-element-boundaries tued-element-hovered-boundaries tued-hidden">
        </div>
        <div class="tued-element-actions tued-hidden">
        </div>
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
        }
    },
    mounted() {
        this.$root.$on('canvas.size.changed', (size) => {
            this.changeSizeTo(size);
        });
    }
};
</script>
