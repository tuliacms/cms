<template>
    <div>
        <div class="mb-3">
            <label class="form-label">Container width</label>
            <div v-for="(label, value) in containerWidthList" :key="value">
                <input type="radio" :id="`tued-section-data-containerWidth-${value}`" :value="value" v-model="section.data.containerWidth" />
                <label :for="`tued-section-data-containerWidth-${value}`">{{ label }}</label>
            </div>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['section']);
const section = inject('sections').manager(props);

const containerWidthList = {
    'default': 'Default',
    'full-width': 'Full width (fluid)',
    'full-width-no-padding': 'Full width (fluid) with no padding',
};
</script>
