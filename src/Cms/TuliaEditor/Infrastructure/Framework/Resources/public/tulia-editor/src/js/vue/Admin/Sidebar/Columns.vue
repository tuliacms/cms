<template>
    <div>
        <vuedraggable
            group="columns"
            item-key="id"
            :list="structureStore.columnsOf(parent)"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `row.${parent}` }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-column > .tued-label > .tued-structure-draggable-handler"
            @start="(event) => emit('draggable-start', event)"
            @change="(event) => emit('draggable-change', event)"
            @end="(event) => emit('draggable-end', event)"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-column">
                    <div
                        @dblclick.stop="emit('selected')"
                        @mouseenter="selectionUseCase.hover(element.id, 'column')"
                        @mouseleave="selectionUseCase.dehover()"
                        @click.stop="selectionUseCase.select(element.id, 'column')"
                        @contextmenu="selectionUseCase.select(element.id, 'column')"
                        :tued-contextmenu="contextmenu.register(element.id, 'column')"
                        :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === element.id, 'tued-element-hovered': selectionStore.hovered.id === element.id }"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selectionUseCase.select(element.id, 'column')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('column') }} {{ element.id }}</span>
<!--                        <div class="tied-structure-element-options">
                            <div class="tued-structure-column-sizer">
                                <span>{{ breakpointSize }}</span>
                                <input
                                    type="text"
                                    data-vueref="'column-' + element.id"
                                    :value="element.sizes[canvas.getBreakpointName()].size"
                                    @focus="$event.target.select()"
                                    @keyup="changeSize(element, $event)"
                                    @keydown="changeSizeWithArrows(element, $event)"
                                    @change="changeSize(element, $event)"
                                    placeholder="inherit"
                                />
                            </div>
                        </div>-->
                    </div>
<!--                    <Blocks
                        :parent="element"
                        :blocks="element.blocks"
                        @draggable-start="(event) => emit('draggable-start', event)"
                        @draggable-change="(event) => emit('draggable-change', event)"
                        @draggable-end="(event) => emit('draggable-end', event)"
                        @selected="emit('selected')"
                    ></Blocks>-->
                </div>
            </template>
        </vuedraggable>
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import { inject, defineEmits, defineProps, onMounted } from "vue";

const props = defineProps(['parent']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end', 'selected']);
const translator = inject('translator');
const structureDragOptions = inject('structureDragOptions');
const selectionUseCase = inject('usecase.selection');
const columnsUseCase = inject('usecase.columns');
const selectionStore = inject('selection.store');
const structureStore = inject('structure.store');
const contextmenu = inject('usecase.contextmenu');

onMounted(() => {
    for (let i in props.columns) {
        columns['column-' + props.columns[i].id] = ref('column-' + props.columns[i].id);
    }

    contextmenu.items('columns', 'column', () => {
        return [
            {
                group: 'column',
                onClick: (id) => columnsUseCase.newBefore(id),
                label: translator.trans('addColumnBefore'),
                icon: 'fas fa-plus',
            },
            {
                group: 'column',
                onClick: (id) => columnsUseCase.newAfter(id),
                label: translator.trans('addColumnAfter'),
                icon: 'fas fa-plus',
            },
            {
                group: 'column',
                onClick: (id) => columnsUseCase.remove(id),
                label: translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            },
        ];
    });
});


/*const { defineProps, computed, inject, ref, onMounted, defineEmits } = require('vue');
const draggable = require('vuedraggable');
const Blocks = require('components/Admin/Sidebar/Blocks.vue').default;
const emits = defineEmits(['selected']);
const props = defineProps(['parent', 'columns']);
const selection = inject('selection.store');
const canvas = inject('canvas');
const columnsSize = inject('columns.size');
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const contextmenu = inject('contextmenu');
const structureManipulator = inject('structureManipulator');

const columns = {};

const breakpointSize = computed (() => {
    return canvas.getBreakpointName();
});

const changeSizeWithArrows = (column, event) => {
    switch (event.key) {
        case '+':
        case 'ArrowUp':
            column.sizes[canvas.getBreakpointName()].size = columnsSize.increment(column, canvas.getBreakpointName());
            break;
        case '-':
        case 'ArrowDown':
            column.sizes[canvas.getBreakpointName()].size = columnsSize.decrement(column, canvas.getBreakpointName());
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
        case 'Backspace':
        case 'Delete':
            break;
        case 'ArrowUp':
        case 'ArrowDown':
        case 'ArrowLeft':
        case 'ArrowRight':
            return;
        default:
            event.preventDefault();
    }

    columnsSize.changeTo(column, canvas.getBreakpointName(), event.target.value);
};

onMounted(() => {
    for (let i in props.columns) {
        columns['column-' + props.columns[i].id] = ref('column-' + props.columns[i].id);
    }
});*/
</script>

