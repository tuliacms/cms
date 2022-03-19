<template>
    <section
        class="tued-structure-section"
        :id="section.id"
        @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'section')"
        @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'section')"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'section')"
    >
        <div class="container-fluid">
            <Row
                v-for="row in section.rows"
                :key="row.id"
                :row="row"
            ></Row>
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
