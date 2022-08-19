<template>
    <div v-if="forms.list">
        <Select :choices="forms.list" :label="props.label" v-model="model"></Select>
    </div>
    <div v-else>
        <i class="fas fa-spinner fa-spin"></i> Loading forms...
    </div>
</template>

<script setup>
const Select = require('controls/Select.vue').default;
const { defineProps, defineEmits, computed, inject, onMounted, reactive } = require('vue');
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    modelValue: { require: true },
    label: { require: true },
});
const options = inject('options');
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
const forms = reactive({list: {}});

onMounted(() => {
    $.ajax({
        url: options.cms_integration.endpoints.form_list,
        method: 'GET',
        success: function(data) {
            forms.list = data;
        }
    });
});
</script>
