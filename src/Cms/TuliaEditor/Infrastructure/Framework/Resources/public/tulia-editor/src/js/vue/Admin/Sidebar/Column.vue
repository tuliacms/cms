<template>
    <div class="tued-structure-element tued-structure-element-column">
        <div
            @dblclick.stop="emit('selected')"
            @mouseenter="selectionUseCase.hover(column.id, 'column')"
            @mouseleave="selectionUseCase.dehover()"
            @click.stop="selectionUseCase.select(column.id, 'column')"
            @contextmenu="selectionUseCase.select(column.id, 'column')"
            :tued-contextmenu="contextmenu.register(column.id, 'column')"
            :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === column.id, 'tued-element-hovered': selectionStore.hovered.id === column.id }"
        >
            <div class="tued-structure-draggable-handler" @mousedown.stop="selectionUseCase.select(column.id, 'column')">
                <i class="fas fa-arrows-alt"></i>
            </div>
            <span>{{ translator.trans('column') }}</span>
            <div class="tied-structure-element-options">
                <div class="tued-structure-column-sizer">
                    <span>{{ storage.breakpointName }}</span>
                    <input
                        type="text"
                        :ref="'column-' + column.id"
                        :value="storage.data.value"
                        @focus="$event.target.select()"
                        @keyup="event => changeSize(event)"
                        @keydown="event => changeSizeWithArrows(event)"
                        @change="event => changeSize(event)"
                        placeholder="inherit"
                    />
                </div>
            </div>
        </div>
        <Blocks
            :parent="column.id"
            @draggable-start="(event) => emit('draggable-start', event)"
            @draggable-change="(event) => emit('draggable-change', event)"
            @draggable-end="(event) => emit('draggable-end', event)"
            @selected="emit('selected')"
        ></Blocks>
    </div>
</template>

<script setup>
import Blocks from "admin/Sidebar/Blocks.vue";
import { inject, defineEmits, defineProps } from "vue";

const props = defineProps(['parent', 'column']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end', 'selected']);
const selectionUseCase = inject('usecase.selection');
const selectionStore = inject('selection.store');
const contextmenu = inject('usecase.contextmenu');
const translator = inject('translator');
const column = inject('instance.columns').manager(props);
const columnSize = inject('columnSize');
const eventBus = inject('eventBus');

const storage = inject('breakpointsAwareDataStorageFactory').ref(column.config.sizes);

const changeSizeWithArrows = (event) => {
    switch (event.key) {
        case '+':
        case 'ArrowUp':
            columnSize.increment(storage);
            break;
        case '-':
        case 'ArrowDown':
            columnSize.decrement(storage);
            break;
    }
};

const changeSize = (event) => {
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

    columnSize.changeTo(storage, event.target.value);
};
</script>
