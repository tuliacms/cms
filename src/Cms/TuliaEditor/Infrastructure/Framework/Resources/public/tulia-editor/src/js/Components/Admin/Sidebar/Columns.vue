<template>
    <div>
        <draggable
            group="columns"
            :list="props.columns"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-column > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="(event) => $emit('draggable-start', event)"
            @change="(event) => $emit('draggable-change', event)"
            @end="(event) => $emit('draggable-end', event)"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-column">
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': element.metadata.selected, 'tued-element-hovered': element.metadata.hovered }"
                        @click.stop="selection.select('column', element.id, 'sidebar')"
                        @mouseenter="selection.hover('column', element.id, 'sidebar')"
                        @mouseleave="selection.resetHovered()"
                        :tued-contextmenu="contextmenu.register('column', element.id)"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('column', element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('column') }}</span>
                        <div class="tied-structure-element-options">
                            <div class="tued-structure-column-sizer">
                                <span>{{ breakpointSize }}</span>
                                <input
                                    type="text"
                                    :ref="'column-' + element.id"
                                    :value="columnSizeByBreakpoint(element)"
                                    @focus="$event.target.select()"
                                    @keyup="changeSize(element, $event)"
                                    @keydown="changeSizeWithArrows(element, $event)"
                                    @change="changeSize(element, $event)"
                                    placeholder="inherit"
                                />
                            </div>
                        </div>
                    </div>
                    <Blocks
                        :parent="element"
                        :blocks="element.blocks"
                        @draggable-start="(event) => $emit('draggable-start', event)"
                        @draggable-change="(event) => $emit('draggable-change', event)"
                        @draggable-end="(event) => $emit('draggable-end', event)"
                    ></Blocks>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script setup>
const { defineProps, computed, inject, ref, onMounted } = require('vue');
const draggable = require('vuedraggable');
const Blocks = require('components/Admin/Sidebar/Blocks.vue').default;
const props = defineProps(['parent', 'columns']);
const selection = inject('selection');
const canvas = inject('canvas');
const columnsSize = inject('columns.size');
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const contextmenu = inject('contextmenu');
const structureManipulator = inject('structureManipulator');

const columns = {};

for (let i in props.columns) {
    columns['column-' + props.columns[i].id] = ref('column-' + props.columns[i].id);
}

const breakpointSize = computed (() => {
    return canvas.getBreakpointName();
});

const changeSizeWithArrows = (column, event) => {
    switch (event.key) {
        case '+':
        case 'ArrowUp':
            columns['column-' + column.id].value = columnsSize.increment(column, canvas.getBreakpointName());
            break;
        case '-':
        case 'ArrowDown':
            columns['column-' + column.id].value = columnsSize.decrement(column, canvas.getBreakpointName());
            break;
        default:
            return;
    }
};

const changeSize = (column, event) => {
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
        case 'ArrowUp':
        case 'ArrowDown':
        case 'ArrowLeft':
        case 'ArrowRight':
        case 'Backspace':
        case 'Delete':
            return;
        default:
            event.preventDefault();
    }

    let input = columns['column-' + column.id];

    input.value = columnsSize.changeTo(column, canvas.getBreakpointName(), input.value);
};

const columnSizeByBreakpoint = (column) => {
    return column.sizes[canvas.getBreakpointName()].size;
};

onMounted(() => {
    contextmenu.items('columns', 'column', () => {
        return [
            {
                group: 'column',
                onClick: (id) => structureManipulator.newColumnBefore(id),
                label: translator.trans('addColumnBefore'),
                icon: 'fas fa-plus',
            },
            {
                group: 'column',
                onClick: (id) => structureManipulator.newColumnAfter(id),
                label: translator.trans('addColumnAfter'),
                icon: 'fas fa-plus',
            },
            {
                group: 'column',
                onClick: (id) => structureManipulator.removeElement(id),
                label: translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            },
        ];
    });
});
</script>

