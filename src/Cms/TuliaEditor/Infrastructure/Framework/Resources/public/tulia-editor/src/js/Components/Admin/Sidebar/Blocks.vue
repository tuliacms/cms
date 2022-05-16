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
                        @click.stop="selection.select('block', element.id, 'sidebar')"
                        @mouseenter="selection.hover('block', element.id, 'sidebar')"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('block', element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ blocksRegistry.get(element.code).name }}</span>
                    </div>
                </div>
            </template>
        </draggable>
        <div class="tued-structure-new-element" @click="blocksPicker.newAt(parent.id)">
            {{ translator.trans('newBlock') }}
        </div>
    </div>
</template>

<script setup>
const { inject, defineProps } = require('vue');
const draggable = require('vuedraggable');

const props = defineProps(['parent', 'blocks']);

const selection = inject('selection');
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const messenger = inject('messenger');
const blocksRegistry = inject('blocks.registry');
const blocksPicker = inject('blocks.picker');

messenger.operation('structure.create.block', (data, success, fail) => {
    blocksPicker.newAt(data.columnId);
    success();
});
</script>
