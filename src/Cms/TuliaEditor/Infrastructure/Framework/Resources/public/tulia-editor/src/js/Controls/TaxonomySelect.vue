<template>
    <div v-if="terms.list">
        <Select :choices="terms.list" :label="props.label" v-model="model" :usesGroups="true"></Select>
    </div>
    <div v-else>
        <i class="fas fa-spinner fa-spin"></i> Loading terms...
    </div>
</template>

<script setup>
const Select = require('controls/Select.vue').default;
const { defineProps, defineEmits, computed, inject, reactive, onMounted } = require('vue');
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

const terms = reactive({list: {}});

onMounted(() => {
    $.ajax({
        url: options.cms_integration.endpoints.taxonomy_term_list,
        method: 'GET',
        type: 'json',
        success: function (data) {
            terms.list = {};

            for (let i in data) {
                let choices = [];

                for (let c in data[i].terms) {
                    choices.push({ value: data[i].terms[c].id, label: data[i].terms[c].name });
                }

                terms.list[i] = {
                    value: data[i].id,
                    label: data[i].name,
                    choices: choices
                };
            }
        }
    });
});
</script>
