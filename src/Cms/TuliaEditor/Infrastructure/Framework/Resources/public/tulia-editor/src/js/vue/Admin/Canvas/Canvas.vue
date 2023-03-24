<template>
    <div class="tued-canvas">
        <div class="tued-canvas-inner">
            <Sizer></Sizer>
            <div @mousedown="selection.deselect()">
                <DeviceFaker :editorView="editorView"></DeviceFaker>
                <div class="body-coturn body-coturn-left" :style="{ transform: `translateX(-${coturnPosition}px)` }"></div>
                <div class="body-coturn body-coturn-right" :style="{ transform: `translateX(${coturnPosition}px)` }"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
import Sizer from "admin/Canvas/Sizer.vue";
import DeviceFaker from "admin/Canvas/DeviceFaker.vue";
import { defineProps, computed, inject } from "vue";

const props = defineProps(['editorView', 'canvasOptions']);
const canvas = inject('canvas');
const selection = inject('usecase.selection');
const coturnPosition = computed(() => canvas.currentBreakpoint.width / 2);
</script>
