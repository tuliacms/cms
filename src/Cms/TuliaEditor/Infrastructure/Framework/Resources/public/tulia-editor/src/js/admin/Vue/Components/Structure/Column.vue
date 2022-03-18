<template>
    <div class="tued-structure-column col" :id="column.id">
        <component
            v-for="block in column.blocks"
            :key="block.id"
            :is="block.type + '-component-frame'"
            :blockData="block.data"
        ></component>
    </div>
</template>

<script>
export default {
    props: ['column']
};
</script>
