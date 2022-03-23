<template>
    <div
        class="tued-structure-row tued-structure-element-selectable row"
        :id="row.id"
        @mouseenter="$root.$emit('structure.hovering.enter', 'row', row.id)"
        @mouseleave="$root.$emit('structure.hovering.leave', 'row', row.id)"
        @mousedown.stop="$root.$emit('structure.selection.select', 'row', row.id)"
        data-tagname="Row"
    >
        <Column
            v-for="column in row.columns"
            :id="'tued-structure-column-' + column.id"
            :key="column.id"
            :column="column"
            :parent="row"
        ></Column>
        <div v-if="row.columns.length === 0">
            Empty Row
        </div>
    </div>
</template>

<script>
import Column from './Column.vue';

export default {
    props: ['row', 'parent'],
    components: { Column },
};
</script>
