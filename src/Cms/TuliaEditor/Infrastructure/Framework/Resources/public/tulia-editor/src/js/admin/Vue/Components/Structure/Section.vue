<template>
    <section class="section" id="{{ section.id }}">
        <Row
            v-for="row in section.rows"
            :key="row.id"
            :row="row"
        ></Row>
    </section>
</template>

<script>
import Row from './Row';

export default {
    props: ['section'],
    components: { Row },
};
</script>
