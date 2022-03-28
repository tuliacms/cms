<template>
    <div>
        <draggable
            group="rows"
            :list="rows"
            v-bind="dragOptions"
            handle=".tued-structure-draggable-placeholder > .tued-structure-element-row > .tued-label > .tued-structure-draggable-handler"
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
                    class="tued-structure-element tued-structure-element-row"
                    v-for="row in rows"
                    :key="'row-' + row.id"
                >
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': selected.id === row.id, 'tued-element-hovered': hovered.id === row.id }"
                        @click.stop="$root.$emit('structure.element.select', 'row', row)"
                        @mouseenter="$root.$emit('structure.element.enter', 'row', row)"
                        @mouseleave="$root.$emit('structure.element.leave', 'row', row)"
                    >
                        <div class="tued-structure-draggable-handler"><i class="fas fa-arrows-alt"></i></div>
                        Wiersz
                    </div>
                    <Columns
                        :parent="row"
                        :columns="row.columns"
                        :selected="selected"
                        :hovered="hovered"
                    ></Columns>
                </div>
            </transition-group>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Columns from './Columns.vue';

export default {
    props: ['parent', 'rows', 'selected', 'hovered'],
    components: {
        draggable,
        Columns
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
