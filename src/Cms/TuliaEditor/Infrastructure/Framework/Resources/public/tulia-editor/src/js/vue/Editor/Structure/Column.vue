<template>
    <div
        :id="'tued-structure-column-' + column.id"
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
import ColumnClassnameGenerator from "core/Editor/View/ColumnClassnameGenerator";
import { inject, defineProps, defineEmits, computed } from "vue";

const props = defineProps(['parent', 'column']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const structure = inject('structure.store');
const contextmenu = inject('contextmenu');
const translator = inject('translator');
const selection = inject('usecase.selection');
const column = inject('instance.columns').editor(props);

const columnClass = computed(() => (new ColumnClassnameGenerator(column, ['tued-structure-column', 'tued-structure-element-selectable'])).generate());
</script>
