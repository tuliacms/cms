<template>
    <Select v-model="column.data._internal.alignment" :label="translator.trans('alignment')" :choices="alignmentChoices"></Select>
    {{ column.data }}
</template>
<script setup>
const { defineProps, inject, reactive, ref, watch, onMounted } = require('vue');
const props = defineProps(['column']);
const column = inject('columns.instance').manager(props);
const translator = inject('translator');
const Select = column.control('Select');

/*const options = reactive({
    alignment: column.data._internal.alignment,
});*/

const alignmentChoices = {
    '': 'Default',
    'align-self-start': 'Align to top',
    'align-self-center': 'Align to middle',
    'align-self-end': 'Align to bottom',
};

watch(() => column.data._internal.alignment, async (newValue, oldValue) => {
    //alert(newValue);
});

onMounted(() => {
    setTimeout(() => {
        column.data._internal.alignment = 'align-self-center';
    }, 3000);
});
</script>
