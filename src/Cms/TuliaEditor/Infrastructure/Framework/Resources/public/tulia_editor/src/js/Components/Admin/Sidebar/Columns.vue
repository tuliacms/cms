<template>
    <div>
        <draggable
            group="columns"
            :list="columns"
            v-bind="dragOptions"
            handle=".tued-structure-element-column > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ name:'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="handleStart"
            @change="handleChange"
            @end="sendDelta"
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
<!--                                <span>{{ canvas.size.breakpoint.name }}</span>
                                <input
                                    type="text"
                                    :ref="'column-' + column.id"
                                    v-model="column.sizes[canvas.size.breakpoint.name].size"
                                    @focus="$event.target.select()"
                                    @keyup="changeSize(column, $event)"
                                    @change="changeSize(column, $event)"
                                    placeholder="inherit"
                                />-->
                            </div>
                        </div>
                    </div>
                    <Blocks
                        :parent="element"
                        :blocks="element.blocks"
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
    props: ['parent', 'columns', 'selected', 'hovered', 'canvas'],
    inject: ['eventDispatcher', 'selection'],
    components: {
        draggable,
        Blocks
    },
    data () {
        return {
            dragOptions: {
                animation: 200,
                disabled: false,
                ghostClass: 'tued-structure-draggable-ghost',
            }
        }
    },
    methods: {
        handleStart: function (event) {
            this.eventDispatcher.emit('structure.element.draggable.start', event);
        },
        handleChange: function (change) {
            this.eventDispatcher.emit('structure.element.draggable.change', change);
        },
        sendDelta: function (event) {
            this.eventDispatcher.emit('structure.element.draggable.stop', event);
        },
        changeSize: function (column, event) {
            let size = column.sizes[this.canvas.size.breakpoint.name];

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
                case 'ArrowLeft':
                case 'ArrowRight':
                case 'ArrowUp':
                case 'ArrowDown':
                case 'Backspace':
                case 'Delete':
                    return;
                default:
                    event.preventDefault();
            }

            let input = this.$refs['column-' + column.id][0];
            let value = input.value;
            let valueInt = parseInt(value);

            // Reset if value is empty or 'zero'
            if (!value) {
                input.value = '';
                size.size = null;
            } else {
                // Max 12 columns
                if (valueInt >= 12) {
                    input.value = '12';
                    valueInt = 12;
                }

                size.size = valueInt;
            }

            this.eventDispatcher.emit('structure.column.resize', column.id, size.size);
        }
    }
};
</script>
