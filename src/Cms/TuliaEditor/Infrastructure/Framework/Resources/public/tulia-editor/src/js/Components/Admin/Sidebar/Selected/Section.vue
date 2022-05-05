<template>
    <div>
        <div class="mb-3">
            <label class="form-label">Container width</label>
            <select class="form-select form-control-raw" v-model="containerWidth">
                <option disabled value="">Please select one</option>
                <option value="1">Default</option>
                <option value="2">Full width</option>
                <option value="3">Full width + gutter free</option>
            </select>
            {{ section.data.containerWidth }}
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, reactive, watch, ref } = require('vue');
const props = defineProps(['section']);
const messenger = inject('messenger');
const translator = inject('translator');
const structureManipulator = inject('structureManipulator');
const section = reactive(props.section);

watch(section.data, async () => {
    structureManipulator.updateElement(section);
});
</script>
