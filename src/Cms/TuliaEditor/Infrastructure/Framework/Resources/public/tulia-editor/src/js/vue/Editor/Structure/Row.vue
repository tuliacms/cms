<template>
    <div
        :id="'tued-structure-row-' + row.id"
        :class="rowClassname"
        @mouseenter="emit('selection-enter', row.id, 'row')"
        @mouseleave="emit('selection-leave', row.id, 'row')"
        @mousedown.stop="selection.select(row.id, 'row', true)"
        data-tagname="Row"
        :tued-contextmenu="contextmenu.register(row.id, 'row')"
    >
        <Column
            v-for="column in structure.columnsOf(row.id)"
            :key="column.id"
            :column="column"
            :parent="row.id"
            @selection-enter="(id, type) => emit('selection-enter', id, type)"
            @selection-leave="(id, type) => emit('selection-leave', id, type)"
        ></Column>
        <div
            class="tued-structure-empty-element"
            v-if="structure.columnsOf(row.id).length === 0"
        >
            <span>{{ translator.trans('emptyRow') }}</span>
        </div>
    </div>
</template>

<script setup>
import { inject, defineProps, defineEmits, computed } from "vue";
import Column from "editor/Structure/Column.vue";
import RowClassnameGenerator from "core/Editor/Render/Row/RowClassnameGenerator";

const props = defineProps(['row', 'parent']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const structure = inject('structure.store');
const contextmenu = inject('contextmenu');
const translator = inject('translator');
const selection = inject('usecase.selection');
const row = inject('structure').row(props.row);
const section = inject('structure').section(props.parent);

const rowClassname = computed(() => RowClassnameGenerator.generate(row, section, 'tued-structure-row tued-structure-element-selectable'));
</script>
