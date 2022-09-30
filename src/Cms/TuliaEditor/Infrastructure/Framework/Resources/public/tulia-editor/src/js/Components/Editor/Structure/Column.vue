<template>
    <div
        :class="columnClass"
        :id="props.column.id"
        @mouseenter="$emit('selection-enter', 'column', props.column.id)"
        @mouseleave="$emit('selection-leave', 'column', props.column.id)"
        @mousedown.stop="selection.select('column', props.column.id, 'editor')"
        data-tagname="Column"
        :tued-contextmenu="contextmenu.register('column', column.id)"
    >
        <Block
            v-for="block in props.column.blocks"
            :id="'tued-structure-block-' + block.id"
            :key="block.id"
            :block="block"
            :parent="props.column"
            @selection-enter="(type, id) => $emit('selection-enter', type, id)"
            @selection-leave="(type, id) => $emit('selection-leave', type, id)"
        ></Block>
        <div
            class="tued-structure-empty-element"
            v-if="props.column.blocks.length === 0"
        >
            {{ translator.trans('emptyColumn') }}
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');
const Block = require('./Block.vue').default;
const SizesClassnameGenerator = require('shared/Structure/Columns/SizesClassnameGenerator.js').default;

const props = defineProps(['column', 'parent']);
const selection = inject('selection');
const translator = inject('translator');
const contextmenu = inject('contextmenu');

const columnClass = computed(() => {
    return (new SizesClassnameGenerator(
        props.column,
        ['tued-structure-column', 'tued-structure-element-selectable']
    )).generate();
});
</script>
