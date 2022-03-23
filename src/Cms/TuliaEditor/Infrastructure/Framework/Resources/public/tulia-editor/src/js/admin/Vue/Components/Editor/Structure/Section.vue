<template>
    <section
        class="tued-structure-section tued-structure-element-selectable"
        :id="section.id"
        @mouseenter="$root.$emit('structure.hovering.enter', 'section', section.id)"
        @mouseleave="$root.$emit('structure.hovering.leave', 'section', section.id)"
        @mousedown.stop="$root.$emit('structure.selection.select', 'section', section.id)"
        data-tagname="Section"
    >
        <div class="container-xxl">
            <Row
                v-for="row in section.rows"
                :id="'tued-structure-row-' + row.id"
                :key="row.id"
                :row="row"
                :parent="section"
            ></Row>
            <div v-if="section.rows.length === 0">
                Empty Section
            </div>
        </div>
    </section>
</template>

<script>
import Row from './Row.vue';

export default {
    props: ['section'],
    components: { Row }
};
</script>
