<template>
    <div class="tued-canvas-sizer">
        <div
            v-for="size in canvas.breakpoints"
            :class="{ 'tued-canvas-size': true, 'active': size.name === canvas.currentBreakpoint.name }"
            :data-breakpoint="size.name" :style="{ width: size.width + 'px' }"
            @click="changeSizeTo(size.name)"
        >
            <span class="tued-canvas-size-left">{{ size.width }}</span>
            <span class="tued-canvas-size-right">{{ size.width }}</span>
        </div>
    </div>
</template>

<script setup>
import { inject } from "vue";
const canvas = inject('canvas');

const changeSizeTo = function (name) {
    canvas.changeSizeTo(name);
};
</script>
