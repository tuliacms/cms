<template>
    <div class="tued-canvas-device-faker" :style="{ width: this.width + 'px' }">
        <iframe :src="editorView"></iframe>
    </div>
</template>

<script>
export default {
    props: ['editorView'],
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
