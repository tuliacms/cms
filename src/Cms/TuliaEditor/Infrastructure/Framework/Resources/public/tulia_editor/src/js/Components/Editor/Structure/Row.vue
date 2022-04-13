<template>
    <div
        class="tued-structure-row tued-structure-element-selectable row"
        :id="row.id"
        @mouseenter="$emit('selection-enter', 'row', row.id)"
        @mouseleave="$emit('selection-leave', 'row', row.id)"
        @mousedown.stop="selection.select('row', row.id)"
        data-tagname="Row"
    >
        <Column
            v-for="column in row.columns"
            :id="'tued-structure-column-' + column.id"
            :key="column.id"
            :column="column"
            :parent="row"
            @selection-enter="(type, id) => $emit('selection-enter', type, id)"
            @selection-leave="(type, id) => $emit('selection-leave', type, id)"
        ></Column>
        <div v-if="row.columns.length === 0">
            Empty Row
        </div>
    </div>
</template>

<script>
const Column = require('./Column.vue').default;

export default {
    props: ['row', 'parent'],
    inject: ['selection'],
    components: { Column },
};
</script>
