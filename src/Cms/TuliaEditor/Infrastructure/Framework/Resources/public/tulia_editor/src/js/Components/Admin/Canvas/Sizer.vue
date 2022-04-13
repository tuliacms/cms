<template>
    <div class="tued-canvas-sizer">
        <div
            v-for="size in canvas.size.breakpoints"
            :class="{ 'tued-canvas-size': true, 'active': size.name === canvas.size.breakpoint.name }"
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
    props: ['container', 'canvas'],
    methods: {
        setCurrentSizeTo: function (name) {
            for (let i in this.canvas.size.breakpoints) {
                if (this.canvas.size.breakpoints[i].name === name) {
                    this.canvas.size.breakpoint.name = name;
                    this.canvas.size.breakpoint.width = this.canvas.size.breakpoints[i].width;
                    this.container.eventDispatcher.emit('device.size.changed', this.canvas.size.breakpoints[i]);
                }
            }
        }
    }
};
</script>
