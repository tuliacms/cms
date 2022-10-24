<template>
    <form :action="endpoint.url" method="post" ref="form">
        <input type="hidden" :value="endpoint.csrfToken" name="_token" />
        <input v-for="(value, name) in fields.list" type="hidden" :name="name" :value="value" />
    </form>
</template>

<script setup>
const Tulia = require('Tulia');
const { defineProps, defineExpose, ref, reactive } = require('vue');
const props = defineProps(['endpoint']);
const fields = reactive({
    list: [],
});

const form = ref(null);
const submit = (newFields) => {
    fields.list = newFields;
    Tulia.PageLoader.show();
    setTimeout(() => form.value.submit(), 300);
};

defineExpose({ submit });
</script>
