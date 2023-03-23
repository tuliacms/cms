<template>
    <div class="mb-3">
        <label v-if="props.label" class="form-label">{{ props.label }}</label>
        <div class="btn-group d-flex" role="group">
            <input type="radio" class="btn-check" v-model="model" :name="`${uniqid}-input`" :id="`${uniqid}-yes`" value="1" autocomplete="off" checked>
            <label class="btn btn-outline-success flex-fill" :for="`${uniqid}-yes`">{{ translator.trans('yes') }}</label>
            <input type="radio" class="btn-check" v-model="model" :name="`${uniqid}-input`" :id="`${uniqid}-no`" value="0" autocomplete="off">
            <label class="btn btn-outline-danger flex-fill" :for="`${uniqid}-no`">{{ translator.trans('no') }}</label>
            <input v-if="props.inheritedOption" type="radio" class="btn-check" v-model="model" :name="`${uniqid}-input`" :id="`${uniqid}-inherited`" value="__null__" autocomplete="off">
            <label v-if="props.inheritedOption" class="btn btn-outline-secondary flex-fill" :for="`${uniqid}-inherited`">{{ translator.trans('inheritValue') }}</label>
        </div>
        <div v-if="props.help" class="form-text">{{ props.help }}</div>
    </div>
</template>
<script setup>
const { defineProps, defineEmits, inject, computed, onMounted } = require('vue');
const _ = require('lodash');
const props = defineProps(['modelValue', 'label', 'help', 'inheritedOption']);
const emit = defineEmits(['update:modelValue']);
const translator = inject('translator');
const model = computed({
    get: () => props.modelValue === null || props.modelValue === '' ? '__null__' : props.modelValue,
    set: (value) => emit('update:modelValue', value === '__null__' ? null : value)
});
const uniqid = _.uniqueId('tued-control-switch-yesno-');
</script>
