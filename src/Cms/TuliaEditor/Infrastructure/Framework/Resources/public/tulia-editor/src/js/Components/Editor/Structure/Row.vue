<template>
    <div
        :class="rowClassname"
        :id="row.id"
        @mouseenter="$emit('selection-enter', 'row', row.id)"
        @mouseleave="$emit('selection-leave', 'row', row.id)"
        @mousedown.stop="selection.select('row', row.id, 'editor')"
        data-tagname="Row"
        :tued-contextmenu="contextmenu.register('row', row.id)"
    >
        <Column
            v-for="column in props.row.columns"
            :id="'tued-structure-column-' + column.id"
            :key="column.id"
            :column="column"
            :parent="row"
            @selection-enter="(type, id) => $emit('selection-enter', type, id)"
            @selection-leave="(type, id) => $emit('selection-leave', type, id)"
        ></Column>
        <div
            class="tued-structure-empty-element"
            v-if="props.row.columns.length === 0"
        >
            <span>{{ translator.trans('emptyRow') }}</span>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');
const Column = require('./Column.vue').default;
const props = defineProps(['row', 'parent']);
const row = inject('rows.instance').editor(props);
const section = row.getParent();
const selection = inject('selection');
const translator = inject('translator');
const contextmenu = inject('contextmenu');

const rowClassname = computed(() => {
    let classname = 'tued-structure-row tued-structure-element-selectable row';

    if (section.data.containerWidth === 'full-width-no-padding') {
        classname += ' g-0';
    }

    return classname;
});
</script>
