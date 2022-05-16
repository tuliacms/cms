<template>
    <div
        :class="columnClass"
        :id="column.id"
        @mouseenter="$emit('selection-enter', 'column', column.id)"
        @mouseleave="$emit('selection-leave', 'column', column.id)"
        @mousedown.stop="selection.select('column', column.id, 'editor')"
        data-tagname="Column"
    >
        <Block
            v-for="block in column.blocks"
            :id="'tued-structure-block-' + block.id"
            :key="block.id"
            :block="block"
            :parent="column"
            @selection-enter="(type, id) => $emit('selection-enter', type, id)"
            @selection-leave="(type, id) => $emit('selection-leave', type, id)"
        ></Block>
        <div v-if="column.blocks.length === 0">
            <div class="tued-structure-new-element" @click="messenger.execute('structure.create.block', { columnId: column.id })">
                {{ translator.trans('newBlock') }}
            </div>
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

const columnClass = computed(() => {
    return (new SizesClassnameGenerator(
        props.column,
        ['tued-structure-column', 'tued-structure-element-selectable']
    )).generate();
});
</script>
