<template>
    <div
        class="tued-structure-row row"
        :id="row.id"
        @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'row')"
        @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'row')"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'row')"
    >
        <Column
            v-for="column in row.columns"
            :key="column.id"
            :column="column"
        ></Column>
    </div>
</template>

<script>
import Column from './Column.vue';

export default {
    props: ['row'],
    components: { Column },
};
</script>
