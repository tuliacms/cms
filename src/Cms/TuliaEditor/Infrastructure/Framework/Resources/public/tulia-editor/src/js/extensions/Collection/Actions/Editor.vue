<template>
    <span v-if="actions.indexOf('moveBackward') !== -1"><button type="button" class="tued-btn" @click="moveBackward()" :title="translator.trans('moveBackward')" v-tooltip><i class="fas fa-backward-fast"></i></button>&nbsp;</span>
    <span v-if="actions.indexOf('moveForward') !== -1"><button type="button" class="tued-btn" @click="moveForward()" :title="translator.trans('moveForward')" v-tooltip><i class="fas fa-forward-fast"></i></button>&nbsp;</span>
    <span v-if="actions.indexOf('moveUp') !== -1"><button type="button" class="tued-btn" @click="moveBackward()" :title="translator.trans('moveUp')" v-tooltip><i class="fas fa-arrow-up"></i></button>&nbsp;</span>
    <span v-if="actions.indexOf('moveDown') !== -1"><button type="button" class="tued-btn" @click="moveForward()" :title="translator.trans('moveDown')" v-tooltip><i class="fas fa-arrow-down"></i></button>&nbsp;</span>
    <span v-if="actions.indexOf('remove') !== -1"><button type="button" class="tued-btn tued-btn-danger" @click="props.collection.remove(props.item)" :title="translator.trans('removeItem')" v-tooltip><i class="fas fa-trash"></i></button>&nbsp;</span>
    <span v-if="actions.indexOf('add') !== -1"><button type="button" class="tued-btn" @click="addNew()">{{ translator.trans('addItem') }}</button>&nbsp;</span>
</template>

<script setup>
const Collection = require('./../Editor.js').default;
const { defineProps, inject, onMounted, defineEmits } = require('vue');
const emits = defineEmits(['movedTo', 'added']);
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
const view = inject('canvas.view');
const actions = props.actions.split(',');

const moveBackward = () => {
    let newPosition = props.collection.moveBackward(props.item);
    if (!!newPosition) {
        emits('movedTo', newPosition);
        view.updated();
    }
};
const moveForward = () => {
    let newPosition = props.collection.moveForward(props.item);
    if (!!newPosition) {
        emits('movedTo', newPosition);
        view.updated();
    }
};
const addNew = () => {
    emits('added', props.collection.add());
    view.updated();
};

onMounted(() => {
    if (!props.collection instanceof Collection) {
        throw new Error('The "collection" property must be instance of "Collection" extension.');
    }
});
</script>
