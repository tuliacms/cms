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
                        Kolumna
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
    props: ['parent', 'columns', 'selected', 'hovered'],
    components: {
        draggable,
        Blocks
    },
    data () {
        return {
            dragOptions: {
                animation: 200,
                disabled: false,
                ghostClass: 'tued-structure-draggable-ghost'
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
        }
    },
};
</script>
