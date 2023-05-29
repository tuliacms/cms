<template>
    <div>
        <Select
            v-model="section.data.containerWidth"
            :choices="containerWidthList"
            :label="translator.trans('containerSize')"
        ></Select>
        <Input
            v-model="section.data.anchorId"
            :label="translator.trans('anchorId')"
            :help="translator.trans('anchorIdHelp')"
        ></Input>
    </div>
</template>
<script setup>
const Select = require('controls/Select.vue').default;
const Input = require('controls/Input.Text.vue').default;
const { defineProps, inject, reactive, watch } = require('vue');
const props = defineProps(['section']);
const section = inject('sections.instance').manager(props);
const translator = inject('translator');

const containerWidthList = {
    'default': translator.trans('containerSizeDefaultWidth'),
    'full-width': translator.trans('containerSizeFullWidth'),
    'full-width-no-padding': translator.trans('containerSizeFullWidthNoPadding'),
};
</script>
