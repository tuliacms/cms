<template>
    <div>
        <draggable
            group="blocks"
            :list="blocks"
            v-bind="dragOptions"
            handle=".tued-structure-element-block > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ name:'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="handleStart"
            @change="handleChange"
            @end="sendDelta"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-block">
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': element.metadata.selected, 'tued-element-hovered': element.metadata.hovered }"
                        @click.stop="selection.select('block', element.id)"
                        @mouseenter="selection.hover('block', element.id)"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('block', element.id)">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>Blok</span>
                    </div>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script>
const draggable = require('vuedraggable');

export default {
    props: ['parent', 'blocks', 'selected', 'hovered'],
    inject: ['eventDispatcher', 'selection'],
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
            this.eventDispatcher.emit('structure.element.draggable.start', event);
        },
        handleChange: function (change) {
            this.eventDispatcher.emit('structure.element.draggable.change', change);
        },
        sendDelta: function (event) {
            this.eventDispatcher.emit('structure.element.draggable.stop', event);
        }
    },
};
</script>
