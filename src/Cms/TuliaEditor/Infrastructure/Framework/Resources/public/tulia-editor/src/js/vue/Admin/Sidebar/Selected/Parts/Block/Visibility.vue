<template>
    <Switch v-model="storage.data.value" :label="translator.trans('visibility')" :help="calculatedVisibilityLabel" inheritedOption="true"></Switch>
</template>
<script setup>
import { defineProps, inject, computed } from "vue";

const props = defineProps(['block']);
const translator = inject('translator');
//const stateCalculator = inject('stateCalculator');
const controls = inject('controls.registry');
const Switch = controls.manager('Switch.YesNo');

const storage = inject('breakpointsAwareDataStorageFactory').ref(props.block.config.visibility);

const calculatedVisibilityLabel = computed(() => {
    return storage.data.value;
    /*if (stateCalculator.calculateState(block, '_internal.visibility') === '0') {
        return translator.trans('calculatedVisibilityInvisible');
    } else {
        return translator.trans('calculatedVisibilityVisible');
    }*/
});
</script>
