<template>
    <div>
        <draggable
            group="columns"
            :list="columns"
            v-bind="dragOptions"
            handle=".tued-structure-draggable-placeholder > .tued-structure-element-column > .tued-label > .tued-structure-draggable-handler"
            @start="handleStart"
            @change="handleChange"
            @end="sendDelta"
        >
            <transition-group
                type="transition"
                class="tued-structure-draggable-placeholder"
                tag="div"
                :data-draggable-delta-transformer-parent="`${parent.type}.${parent.id}`"
            >
                <div
                    class="tued-structure-element tued-structure-element-column"
                    v-for="column in columns"
                    :key="'column-' + column.id"
                >
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': selected.id === column.id, 'tued-element-hovered': hovered.id === column.id }"
                        @click.stop="$root.$emit('structure.element.select', 'column', column)"
                        @mouseenter="$root.$emit('structure.element.enter', 'column', column)"
                        @mouseleave="$root.$emit('structure.element.leave', 'column', column)"
                    >
                        <div class="tued-structure-draggable-handler"><i class="fas fa-arrows-alt"></i></div>
                        <span>Kolumna</span>
                        <div class="tied-structure-element-options">
                            <div class="tued-structure-column-sizer">
                                <span>{{ canvas.size.breakpoint.name }}</span>
                                <input
                                    type="text"
                                    :ref="'column-' + column.id"
                                    v-model="column.sizes[canvas.size.breakpoint.name].size"
                                    @focus="$event.target.select()"
                                    @keyup="changeSize(column, $event)"
                                    @change="changeSize(column, $event)"
                                    placeholder="inherit"
                                />
                            </div>
                        </div>
                    </div>
                    <Blocks
                        :parent="column"
                        :blocks="column.blocks"
                        :selected="selected"
                        :hovered="hovered"
                    ></Blocks>
                </div>
            </transition-group>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Blocks from './Blocks.vue';

export default {
    props: ['parent', 'columns', 'selected', 'hovered', 'messenger', 'canvas'],
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
            this.$root.$emit('structure.element.draggable.start', event);
        },
        handleChange: function (change) {
            this.$root.$emit('structure.element.draggable.change', change);
        },
        sendDelta: function (event) {
            this.$root.$emit('structure.element.draggable.stop', event);
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

            this.$root.$emit('structure.column.resize', column.id, size.size);
        }
    }
};
</script>
