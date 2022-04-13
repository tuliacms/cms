<template>
    <section
        class="tued-structure-section tued-structure-element-selectable"
        :id="section.id"
        @mouseenter="$emit('selection-enter', 'section', section.id)"
        @mouseleave="$emit('selection-leave', 'section', section.id)"
        @mousedown.stop="selection.select('section', section.id)"
        data-tagname="Section"
    >
        <div class="container-xxl">
            <Row
                v-for="row in section.rows"
                :id="'tued-structure-row-' + row.id"
                :key="row.id"
                :row="row"
                :parent="section"
                @selection-enter="(type, id) => $emit('selection-enter', type, id)"
                @selection-leave="(type, id) => $emit('selection-leave', type, id)"
            ></Row>
            <div v-if="section.rows.length === 0">
                Empty Section
            </div>
        </div>
    </section>
</template>

<script>
const Row = require('./Row.vue').default;

export default {
    props: ['section'],
    inject: ['selection'],
    components: { Row }
};
</script>
