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
                        @dblclick.stop="emits('selected')"
                        @mouseenter="selection.hover('block', element.id, 'sidebar')"
                        @mouseleave="selection.resetHovered()"
                        :tued-contextmenu="contextmenu.register('block', element.id)"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('block', element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ blocksRegistry.get(element.code).name }}</span>
                    </div>
                </div>
            </template>
        </draggable>
<!--        <div class="tued-structure-new-element" @click="blocksPicker.newAt(parent.id)">
            {{ translator.trans('newBlock') }}
        </div>-->
    </div>
</template>

<script setup>
const { inject, defineProps, defineEmits, onMounted } = require('vue');
const draggable = require('vuedraggable');

const props = defineProps(['parent', 'blocks']);
const emits = defineEmits(['selected']);

const selection = inject('selection');
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const messenger = inject('messenger');
const blocksRegistry = inject('blocks.registry');
const blocksPicker = inject('blocks.picker');
const contextmenu = inject('contextmenu');
const structureManipulator = inject('structureManipulator');

onMounted(() => {
    contextmenu.items('blocks', 'column', () => {
        return [
            {
                onClick: (id) => blocksPicker.newAt(id),
                label: translator.trans('addBlock'),
                icon: 'fas fa-plus',
            },
        ];
    });
    contextmenu.items('blocks', 'block', () => {
        return [
            {
                group: 'block',
                onClick: (id) => structureManipulator.removeElement(id),
                label: translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            },
        ];
    });
});
</script>
