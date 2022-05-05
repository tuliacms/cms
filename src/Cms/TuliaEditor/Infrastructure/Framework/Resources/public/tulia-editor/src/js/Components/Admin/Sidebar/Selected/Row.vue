<template>
    <div>
        <div class="mb-3">
            <label class="form-label">Gutters</label>
            <div v-for="(label, value) in gutterOptionsList" :key="value">
                <input type="radio" :id="`tued-row-data-gutters-${value}`" :value="value" v-model="row.data.gutters" />
                <label :for="`tued-row-data-gutters-${value}`">{{ label }}</label>
            </div>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['row']);
const row = inject('rows').manager(props);

const gutterOptionsList = {
    'default': 'Default gutters',
    'no-gutters': 'No gutters',
};
</script>
