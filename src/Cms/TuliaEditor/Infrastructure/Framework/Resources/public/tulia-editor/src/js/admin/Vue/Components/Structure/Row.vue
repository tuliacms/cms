<template>
    <div
        class="tued-structure-row tued-structure-element-selectable row"
        :id="row.id"
        @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'row')"
        @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'row')"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'row', row, parent)"
        data-tagname="Row"
    >
        <Column
            v-for="(column, key) in row.columns"
            :key="'column-' + key"
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
