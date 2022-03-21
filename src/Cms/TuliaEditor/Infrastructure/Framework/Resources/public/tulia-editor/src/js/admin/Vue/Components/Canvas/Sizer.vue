<template>
    <div class="tued-canvas-sizer">
        <div
            v-for="size in sizes"
            :class="{ 'tued-canvas-size': true, 'active': size.name === activeSize }"
            :data-breakpoint="size.name" :style="{ width: size.width + 'px' }"
            @click="setCurrentSizeTo(size.name)"
        >
            <span class="tued-canvas-size-left">{{ size.width }}</span>
            <span class="tued-canvas-size-right">{{ size.width }}</span>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            activeSize: 'xl',
            sizes: [
                { name: 'xxl', width: 1400 },
                { name: 'xl', width: 1200 },
                { name: 'lg', width: 992 },
                { name: 'md', width: 768 },
                { name: 'sm', width: 576 },
                { name: 'xs', width: 380 },
            ]
        }
    },
    methods: {
        setCurrentSizeTo: function (name) {
            for (let i in this.sizes) {
                if (this.sizes[i].name === name) {
                    this.activeSize = name;
                    this.$root.$emit('device.size.changed', this.sizes[i]);
                }
            }
        }
    }
};
</script>
