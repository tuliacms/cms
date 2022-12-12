<template>
    <Switch v-model="storage.data.value" :label="translator.trans('visibility')" inheritedOption="true"></Switch>
</template>
<script setup>
const BreakpointsAwareDataStorage = require('shared/Structure/Element/Data/BreakpointsAwareDataStorage.js').default;
const { defineProps, inject, reactive, ref, watch } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');
const Switch = block.control('Switch.YesNo');

const storage = BreakpointsAwareDataStorage.ref(block, '_internal.visibility');
</script>
