<template>
    <section
        class="tued-structure-section tued-structure-element-selectable"
        :id="section.id"
        @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'section')"
        @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'section')"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'section')"
        data-tagname="Section"
    >
        <div class="container-xxl">
            <Row
                v-for="(row, key) in section.rows"
                :key="'row-' + key"
                :row="row"
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
