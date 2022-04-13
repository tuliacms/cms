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

export default {
    props: ['column', 'parent'],
    inject: ['selection'],
    components: {Block},
    computed: {
        columnClass: function () {
            let classList = ['tued-structure-column', 'tued-structure-element-selectable'];
            let anySizingAdded = false;

            for (let i in this.column.sizes) {
                if (this.column.sizes[i].size) {
                    let prefix = `${i}-`;

                    if (i === 'xs') {
                        prefix = '';
                    }

                    classList.push(`col-${prefix}${this.column.sizes[i].size}`);
                    anySizingAdded = true;
                }
            }

            if (anySizingAdded === false) {
                classList.push('col');
            }

            return classList;
        }
    }
};
</script>
