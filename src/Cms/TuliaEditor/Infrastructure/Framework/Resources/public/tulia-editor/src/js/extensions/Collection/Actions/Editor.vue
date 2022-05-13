<template>
    <button v-if="actions.indexOf('moveBackward') !== -1" type="button" class="tued-btn" @click="props.collection.moveBackward(props.item)" :title="translator.trans('moveBackward')"><i class="fas fa-arrow-left"></i></button>
    <button v-if="actions.indexOf('moveForward') !== -1" type="button" class="tued-btn" @click="props.collection.moveForward(props.item)" :title="translator.trans('moveForward')"><i class="fas fa-arrow-right"></i></button>
    <button v-if="actions.indexOf('moveUp') !== -1" type="button" class="tued-btn" @click="props.collection.moveBackward(props.item)" :title="translator.trans('moveUp')"><i class="fas fa-arrow-up"></i></button>
    <button v-if="actions.indexOf('moveDown') !== -1" type="button" class="tued-btn" @click="props.collection.moveForward(props.item)" :title="translator.trans('moveDown')"><i class="fas fa-arrow-down"></i></button>
    <button v-if="actions.indexOf('remove') !== -1" type="button" class="tued-btn tued-btn-danger" @click="props.collection.remove(props.item)" :title="translator.trans('removeItem')"><i class="fas fa-trash"></i></button>
    <button v-if="actions.indexOf('add') !== -1" type="button" class="tued-btn" @click="props.collection.add()">{{ translator.trans('addItem') }}</button>
</template>

<script setup>
const { defineProps, inject, onMounted } = require('vue');
const props = defineProps({
    actions: {
        require: true,
    },
    collection: {
        require: true,
    },
    item: {},
});
const translator = inject('translator');
const actions = props.actions.split(',');

const isCollectionObject = (object) => {
    return typeof object.moveBackward === 'function'
        && typeof object.moveForward === 'function'
        && typeof object.moveToIndex === 'function'
    ;
};

onMounted(() => {
    if (!isCollectionObject(props.collection)) {
        throw new Error('The "collection" property must be instance of "Collection" extension.');
    }
});
</script>
