<template>
    <div>
        <draggable
            group="blocks"
            :list="blocks"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-block > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="(event) => $emit('draggable-start', event)"
            @change="(event) => $emit('draggable-change', event)"
            @end="(event) => $emit('draggable-end', event)"
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
                        <span>{{ translator.trans('block') }}</span>
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
    inject: ['selection', 'structureDragOptions', 'translator'],
    components: {
        draggable,
    }
};
</script>
