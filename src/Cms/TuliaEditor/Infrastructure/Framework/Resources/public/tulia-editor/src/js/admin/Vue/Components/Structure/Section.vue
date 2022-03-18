<template>
    <section class="tued-structure-section" :id="section.id">
        <div class="container-fluid">
            <Row
                v-for="row in section.rows"
                :key="row.id"
                :row="row"
                :availableBlocks="availableBlocks"
            ></Row>
        </div>
    </section>
</template>

<script>
import Row from './Row.vue';

export default {
    props: ['section', 'availableBlocks'],
    components: { Row },
};
</script>
