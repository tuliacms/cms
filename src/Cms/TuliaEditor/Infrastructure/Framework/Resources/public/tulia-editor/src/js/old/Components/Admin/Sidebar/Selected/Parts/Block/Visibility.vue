<template>
    <Switch v-model="storage.data.value" :label="translator.trans('visibility')" :help="calculatedVisibilityLabel" inheritedOption="true"></Switch>
</template>
<script setup>
const BreakpointsAwareDataStorage = require('shared/Structure/Element/Data/BreakpointsAwareDataStorage.js').default;
const { defineProps, inject, reactive, ref, watch, computed } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');
const stateCalculator = inject('stateCalculator');
const Switch = block.control('Switch.YesNo');

const storage = BreakpointsAwareDataStorage.ref(block, '_internal.visibility');
const calculatedVisibilityLabel = computed(() => {
    if (stateCalculator.calculateState(block, '_internal.visibility') === '0') {
        return translator.trans('calculatedVisibilityInvisible');
    } else {
        return translator.trans('calculatedVisibilityVisible');
    }
});
</script>
