<template>
    <div
        :id="'tued-structure-column-' + column.id"
        :class="columnClass"
        @mouseenter="emit('selection-enter', column.id, 'column')"
        @mouseleave="emit('selection-leave', column.id, 'column')"
        @mousedown.stop="selection.select(column.id, 'column', true)"
        data-tagname="Column"
        :tued-contextmenu="contextmenu.register(column.id, 'column')"
    >
        <Block
             v-for="block in structure.blocksOf(column.id)"
             :key="block.id"
             :block="block"
             :parent="column.id"
             @selection-enter="(id, type) => emit('selection-enter', id, type)"
             @selection-leave="(id, type) => emit('selection-leave', id, type)"
        ></Block>
        <div
            class="tued-structure-empty-element"
            v-if="props.column.blocks.length === 0"
        >
            <span>{{ translator.trans('emptyColumn') }}</span>
        </div>
    </div>
</template>

<script setup>
import ColumnClassnameGenerator from "core/Editor/Render/Column/ColumnClassnameGenerator";
import Block from "editor/Structure/Block.vue";
import { inject, defineProps, defineEmits, computed } from "vue";

const props = defineProps(['parent', 'column']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const structure = inject('structure.store');
const contextmenu = inject('contextmenu');
const translator = inject('translator');
const selection = inject('usecase.selection');
const column = inject('structure').column(props.column);

const columnClass = computed(() => ColumnClassnameGenerator.generate(column, ['tued-structure-column', 'tued-structure-element-selectable']));
</script>
