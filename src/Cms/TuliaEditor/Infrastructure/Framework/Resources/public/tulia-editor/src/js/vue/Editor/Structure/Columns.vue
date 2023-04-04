<template>
    <div
        v-for="column in structure.columnsOf(parent)"
        :id="'tued-structure-column-' + column.id"
        :key="column.id"
        :class="columnClass"
        @mouseenter="emit('selection-enter', column.id, 'column')"
        @mouseleave="emit('selection-leave', column.id, 'column')"
        @mousedown.stop="selection.select(column.id, 'column')"
        data-tagname="Column"
        :tued-contextmenu="contextmenu.register(column.id, 'column')"
    >
<!--        <Block
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
            <span>{{ translator.trans('emptyColumn') }}</span>
        </div>-->
        <div
            class="tued-structure-empty-element"
        >
            <span>{{ translator.trans('emptyColumn') }}</span>
        </div>
    </div>
</template>

<script setup>
import { inject, defineProps, defineEmits } from "vue";

const props = defineProps(['parent']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const structure = inject('structure.store');
const contextmenu = inject('contextmenu');
const translator = inject('translator');
const selection = inject('usecase.selection');
const columnClass = 'col';
/*const { defineProps, inject, computed } = require('vue');
const Block = require('./Block.vue').default;
const SizesClassnameGenerator = require('shared/Structure/Columns/SizesClassnameGenerator.js').default;

const props = defineProps(['column', 'parent']);
const selection = inject('selection');
const translator = inject('translator');
const contextmenu = inject('contextmenu');

const columnClass = computed(() => (new SizesClassnameGenerator(props.column, ['tued-structure-column', 'tued-structure-element-selectable'])).generate());*/
</script>
