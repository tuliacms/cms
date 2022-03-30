<template>
    <div
        :class="columnClass"
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
    props: ['column', 'parent'],
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
