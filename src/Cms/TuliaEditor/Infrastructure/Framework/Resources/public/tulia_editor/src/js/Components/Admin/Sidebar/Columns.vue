<template>
    <div>
        <draggable
            group="columns"
            :list="columns"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-column > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="(event) => $emit('draggable-start', event)"
            @change="(event) => $emit('draggable-change', event)"
            @end="(event) => $emit('draggable-end', event)"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-column">
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': element.metadata.selected, 'tued-element-hovered': element.metadata.hovered }"
                        @click.stop="selection.select('column', element.id)"
                        @mouseenter="selection.hover('column', element.id)"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('column', element.id)">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>Kolumna</span>
                        <div class="tied-structure-element-options">
                            <div class="tued-structure-column-sizer">
                                <span>{{ breakpointSize }}</span>
                                <input
                                    type="text"
                                    :ref="'column-' + element.id"
                                    :value="columnSizeByBreakpoint(element)"
                                    @focus="$event.target.select()"
                                    @keyup="changeSize(element, $event)"
                                    @keydown="changeSizeWithArrows(element, $event)"
                                    @change="changeSize(element, $event)"
                                    placeholder="inherit"
                                />
                            </div>
                        </div>
                    </div>
                    <Blocks
                        :parent="element"
                        :blocks="element.blocks"
                        @draggable-start="(event) => $emit('draggable-start', event)"
                        @draggable-change="(event) => $emit('draggable-change', event)"
                        @draggable-end="(event) => $emit('draggable-end', event)"
                    ></Blocks>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script>
const draggable = require('vuedraggable');
const Blocks = require('components/Admin/Sidebar/Blocks.vue').default;

export default {
    props: ['parent', 'columns', 'selected', 'hovered'],
    inject: ['selection', 'canvas', 'columnSize', 'structureDragOptions'],
    components: {
        draggable,
        Blocks
    },
    computed: {
        breakpointSize: function () {
            return this.canvas.getBreakpointName();
        },
    },
    methods: {
        changeSizeWithArrows: function (column, event) {
            switch (event.key) {
                case '+':
                case 'ArrowUp':
                    this.$refs['column-' + column.id].value = this.columnSize.increment(column, this.canvas.getBreakpointName());
                    break;
                case '-':
                case 'ArrowDown':
                    this.$refs['column-' + column.id].value = this.columnSize.decrement(column, this.canvas.getBreakpointName());
                    break;
                default:
                    return;
            }
        },
        changeSize: function (column, event) {
            switch (event.key) {
                case '1':
                case '2':
                case '3':
                case '4':
                case '5':
                case '6':
                case '7':
                case '8':
                case '9':
                case '0':
                    break;
                case 'ArrowUp':
                case 'ArrowDown':
                case 'ArrowLeft':
                case 'ArrowRight':
                case 'Backspace':
                case 'Delete':
                    return;
                default:
                    event.preventDefault();
            }

            let input = this.$refs['column-' + column.id];

            input.value = this.columnSize.changeTo(column, this.canvas.getBreakpointName(), input.value);
        },
        columnSizeByBreakpoint: function (column) {
            return column.sizes[this.canvas.getBreakpointName()].size;
        },
    }
};
</script>
