<template>
    <div>
        <draggable
            group="rows"
            :list="rows"
            v-bind="dragOptions"
            handle=".tued-structure-element-row > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ name:'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="handleStart"
            @change="handleChange"
            @end="sendDelta"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-row">
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': element.metadata.selected, 'tued-element-hovered': element.metadata.hovered }"
                        @click.stop="selection.select('row', element.id)"
                        @mouseenter="selection.hover('row', element.id)"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('row', element.id)">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>Wiersz</span>
                    </div>
                    <Columns
                        :parent="element"
                        :columns="element.columns"
                    ></Columns>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script>
const draggable = require('vuedraggable');
const Columns = require('components/Admin/Sidebar/Columns.vue').default;

export default {
    props: ['parent', 'rows'],
    inject: ['eventDispatcher', 'selection'],
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
