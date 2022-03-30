<template>
    <div>
        <draggable
            group="blocks"
            :list="blocks"
            v-bind="dragOptions"
            handle=".tued-structure-draggable-placeholder > .tued-structure-element-block > .tued-label > .tued-structure-draggable-handler"
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
                    class="tued-structure-element tued-structure-element-block"
                    v-for="block in blocks"
                    :key="'block-' + block.id"
                >
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': selected.id === block.id, 'tued-element-hovered': hovered.id === block.id }"
                        @click.stop="$root.$emit('structure.element.select', 'block', block)"
                        @mouseenter="$root.$emit('structure.element.enter', 'block', block)"
                        @mouseleave="$root.$emit('structure.element.leave', 'block', block)"
                    >
                        <div class="tued-structure-draggable-handler"><i class="fas fa-arrows-alt"></i></div>
                        <span>Blok</span>
                    </div>
                </div>
            </transition-group>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
    props: ['parent', 'blocks', 'selected', 'hovered'],
    components: {
        draggable,
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
