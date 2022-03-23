<template>
    <div
        class="tued-structure-column tued-structure-element-selectable col"
        :id="column.id"
        @mouseenter="$root.$emit('structure.hovering.enter', 'column', column.id)"
        @mouseleave="$root.$emit('structure.hovering.leave', 'column', column.id)"
        @mousedown.stop="$root.$emit('structure.selection.select', 'column', column.id)"
        data-tagname="Column"
    >
        <component
            v-for="block in column.blocks"
            :data-block="block.block_type + '-component-frame'"
            :id="'tued-structure-block-' + block.id"
            :key="block.id"
            :is="block.block_type + '-component-frame'"
            :block="block"
            :parent="column"
        ></component>
        <div v-if="column.blocks.length === 0">
            Empty Column
        </div>
    </div>
</template>

<script>
export default {
    props: ['column', 'parent']
};
</script>
