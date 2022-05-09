<template>
    <div
        :class="rowClassname"
        :id="row.id"
        @mouseenter="$emit('selection-enter', 'row', row.id)"
        @mouseleave="$emit('selection-leave', 'row', row.id)"
        @mousedown.stop="selection.select('row', row.id, 'editor')"
        data-tagname="Row"
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
        <div v-if="props.row.columns.length === 0">
            Empty Row
        </div>
    </div>
</template>

<script setup>
const Column = require('./Column.vue').default;
const { defineProps, inject, computed } = require('vue');
const props = defineProps(['row', 'parent']);
const row = inject('rows.instance').editor(props);
const selection = inject('selection');

const rowClassname = computed(() => {
    let classname = 'tued-structure-row tued-structure-element-selectable row';

    switch (row.data.gutters) {
        case 'no-gutters': classname += ' g-0'; break;
    }

    return classname;
});
</script>
