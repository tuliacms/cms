<template>
    <div
        :class="columnClass"
        :id="column.id"
        @mouseenter="$emit('selection-enter', 'column', column.id)"
        @mouseleave="$emit('selection-leave', 'column', column.id)"
        @mousedown.stop="selection.select('column', column.id)"
        data-tagname="Column"
    >
        <Block
            v-for="block in column.blocks"
            :id="'tued-structure-block-' + block.id"
            :key="block.id"
            :block="block"
            :parent="column"
            @selection-enter="(type, id) => $emit('selection-enter', type, id)"
            @selection-leave="(type, id) => $emit('selection-leave', type, id)"
        ></Block>
        <div v-if="column.blocks.length === 0">
            Empty Column
        </div>
    </div>
</template>

<script>
const Block = require('./Block.vue').default;
const SizesClassnameGenerator = require('shared/Structure/Columns/SizesClassnameGenerator.js').default;

export default {
    props: ['column', 'parent'],
    inject: ['selection'],
    components: {Block},
    computed: {
        columnClass: function () {
            return (new SizesClassnameGenerator(
                this.column,
                ['tued-structure-column', 'tued-structure-element-selectable']
            )).generate();
        }
    }
};
</script>
