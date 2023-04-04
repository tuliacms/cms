<template>
    <div
        v-for="row in structure.rowsOf(parent)"
        :id="'tued-structure-row-' + row.id"
        :key="row.id"
        :class="rowClassname"
        @mouseenter="emit('selection-enter', row.id, 'row')"
        @mouseleave="emit('selection-leave', row.id, 'row')"
        @mousedown.stop="selection.select(row.id, 'row')"
        data-tagname="Row"
        :tued-contextmenu="contextmenu.register(row.id, 'row')"
    >
        <Columns
            :parent="row.id"
            @selection-enter="(type, id) => $emit('selection-enter', type, id)"
            @selection-leave="(type, id) => $emit('selection-leave', type, id)"
        ></Columns>
        <div
            class="tued-structure-empty-element"
        >
            <span>{{ translator.trans('emptyRow') }}</span>
        </div>
<!--        <div
            class="tued-structure-empty-element"
            v-if="props.row.columns.length === 0"
        >
            <span>{{ translator.trans('emptyRow') }}</span>
        </div>-->
    </div>
</template>

<script setup>
import { inject, defineProps, defineEmits } from "vue";
import Columns from "editor/Structure/Columns.vue";

const props = defineProps(['parent']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const structure = inject('structure.store');
const contextmenu = inject('contextmenu');
const translator = inject('translator');
const selection = inject('usecase.selection');
const rowClassname = 'tued-structure-row tued-structure-element-selectable row';

/*const { defineProps, inject, computed } = require('vue');
const props = defineProps(['row', 'parent']);
const row = inject('rows.instance').editor(props);
const section = row.getParent();
const selection = inject('selection');
const translator = inject('translator');

const rowClassname = computed(() => {
    let classname = 'tued-structure-row tued-structure-element-selectable row';

    if (section.data.containerWidth === 'full-width-no-padding') {
        classname += ' g-0';
    }

    return classname;
});*/
</script>
