<template>
    <div
        class="tued-structure-column tued-structure-element-selectable col"
        :id="column.id"
        @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'column')"
        @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'column')"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'column')"
        data-tagname="Column"
    >
        <component
            v-for="(block, key) in column.blocks"
            :key="'block-' + key"
            :is="block.type + '-component-frame'"
            :blockData="block.data"
        ></component>
        <div v-if="column.blocks.length === 0">
            Empty Column
        </div>
    </div>
</template>

<script>
export default {
    props: ['column']
};
</script>
