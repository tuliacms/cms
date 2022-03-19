<template>
    <div class="tued-structure-row row" :id="row.id">
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
