<template>
    <div class="tued-canvas-sizer">
        <div
            v-for="size in canvasOptions.size.breakpoints"
            :class="{ 'tued-canvas-size': true, 'active': size.name === canvasOptions.size.breakpoint.name }"
            :data-breakpoint="size.name" :style="{ width: size.width + 'px' }"
            @click="changeSizeTo(size.name)"
        >
            <span class="tued-canvas-size-left">{{ size.width }}</span>
            <span class="tued-canvas-size-right">{{ size.width }}</span>
        </div>
    </div>
</template>

<script>
export default {
    props: ['container', 'canvasOptions'],
    inject: ['canvas'],
    methods: {
        changeSizeTo: function (name) {
            this.canvas.changeSizeTo(name);
        }
    }
};
</script>
