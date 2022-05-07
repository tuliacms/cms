<template>
    <div>
        <div class="mb-3">
            <label class="form-label">Container width</label>
            <div v-for="(label, value) in containerWidthList" :key="value">
                <input type="radio" :id="`tued-section-data-containerWidth-${value}`" :value="value" v-model="containerWidth" />
                <label :for="`tued-section-data-containerWidth-${value}`">{{ label }}</label>
            </div>
        </div>
    </div>
</template>
<script setup>
const { defineProps, inject, ref, watch } = require('vue');
const props = defineProps(['section']);
const section = inject('sections').manager(props);


/******************
 * Container width
 ******************/
const containerWidthList = {
    'default': 'Default width',
    'full-width': 'Full width (fluid)',
    'full-width-no-padding': 'Full width (fluid) with no padding',
};
const containerWidth = ref(section.data.containerWidth);
watch(containerWidth, async () => {
    section.data.containerWidth = containerWidth.value;

    if (containerWidth.value === 'full-width-no-padding') {
        setAllRowsDataValue('gutters', 'no-gutters');
    } else {
        setAllRowsDataValue('gutters', 'default');
    }
});

/******************
 * Rows management
 *****************/
const setAllRowsDataValue = (key, value) => {
    for (let i in section.children) {
        section.children[i].data[key] = value;
    }
};
</script>
