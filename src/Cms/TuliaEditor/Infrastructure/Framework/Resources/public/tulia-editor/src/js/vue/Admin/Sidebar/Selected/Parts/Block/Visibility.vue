<template>
    <Switch v-model="storage.data.value" :label="translator.trans('visibility')" :help="calculatedVisibilityLabel" inheritedOption="true"></Switch>
</template>
<script setup>
import { defineProps, inject, computed } from "vue";

const props = defineProps(['block']);
const translator = inject('translator');
const controls = inject('controls.registry');
const Switch = controls.manager('Switch.YesNo');

const storage = inject('breakpointsAwareDataStorageFactory').ref(props.block.config.visibility);
const stateCalculator = inject('breakpointsStateCalculatorFactory').fromStorage(storage);

const calculatedVisibilityLabel = computed(() => {
    if (stateCalculator.calculateState() === '0') {
        return translator.trans('calculatedVisibilityInvisible');
    } else {
        return translator.trans('calculatedVisibilityVisible');
    }
});
</script>
<script>
export default {
    name: 'Admin.Sidebar.Selected.Part.Block.Visibility',
}
</script>
