<template>
    <div>
        <vuedraggable
            group="rows"
            item-key="id"
            :list="structureStore.rowsOf(parent)"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `section.${parent}` }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-row > .tued-label > .tued-structure-draggable-handler"
            @start="(event) => emit('draggable-start', event)"
            @change="(event) => emit('draggable-change', event)"
            @end="(event) => emit('draggable-end', event)"
        >
            <template #item="{element}">
                <div
                    class="tued-structure-element tued-structure-element-row"
                    data-element-type="row"
                    :data-element-id="element.id"
                >
                    <div
                        @dblclick.stop="emit('selected')"
                        @mouseenter="selectionUseCase.hover(element.id, 'row')"
                        @mouseleave="selectionUseCase.dehover()"
                        @click.stop="selectionUseCase.select(element.id, 'row')"
                        @contextmenu="selectionUseCase.select(element.id, 'row')"
                        :tued-contextmenu="contextmenu.register(element.id, 'row')"
                        :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === element.id, 'tued-element-hovered': selectionStore.hovered.id === element.id }"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selectionUseCase.select(element.id, 'row')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('row') }}</span>
                    </div>
                    <Columns
                        :parent="element.id"
                        @draggable-start="(event) => emit('draggable-start', event)"
                        @draggable-change="(event) => emit('draggable-change', event)"
                        @draggable-end="(event) => emit('draggable-end', event)"
                        @selected="emit('selected')"
                    ></Columns>
                </div>
            </template>
        </vuedraggable>
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import Columns from "admin/Sidebar/Columns.vue";
import { inject, defineEmits, defineProps, onMounted } from "vue";

const props = defineProps(['parent']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end', 'selected']);
const translator = inject('translator');
const structureDragOptions = inject('structureDragOptions');
const selectionUseCase = inject('usecase.selection');
const rowsUseCase = inject('usecase.rows');
const columnsUseCase = inject('usecase.columns');
const selectionStore = inject('selection.store');
const structureStore = inject('structure.store');
const contextmenu = inject('usecase.contextmenu');

onMounted(() => {
    contextmenu.items('rows', 'row', (id) => {
        const items = [];

        items.push({
            group: 'row',
            onClick: (id) => columnsUseCase.newOne(id),
            label: translator.trans('addColumn'),
            icon: 'fas fa-plus',
        });

        items.push({
            group: 'row',
            onClick: (id) => rowsUseCase.remove(id),
            label: translator.trans('delete'),
            icon: 'fas fa-trash',
            classname: 'dropdown-item-danger',
        });

        return items;
    });
});
</script>
