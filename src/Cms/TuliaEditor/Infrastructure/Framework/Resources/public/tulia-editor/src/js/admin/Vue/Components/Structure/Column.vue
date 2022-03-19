<template>
    <div
        class="tued-structure-column col"
        :id="column.id"
        @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'column')"
        @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'column')"
    >
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
