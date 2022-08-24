<template>
    <div>
        Filemanager :)
    </div>
</template>

<script setup>
const { defineProps, onMounted, provide, reactive, isProxy, toRaw } = require('vue');

const props = defineProps([
    'editor',
    'container',
    'instanceId',
    'options',
    'availableBlocks',
    'canvas',
    'structure'
]);
</script>
