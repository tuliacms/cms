<template>
    <div class="tued-sidebar-structure">
        <vuedraggable
            group="sections"
            item-key="id"
            :list="structureStore.sections"
            tag="div"
            :component-data="{ name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': '' }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-section > .tued-label > .tued-structure-draggable-handler"
            @change="changeDraggable"
            @start="startDraggable"
            @end="endDraggable"
        >
            <template #item="{element}">
                <div
                    class="tued-structure-element tued-structure-element-section"
                    data-element-type="section"
                    :data-element-id="element.id"
                >
                    <div
                        @dblclick.stop="emit('selected')"
                        @mouseenter="selectionUseCase.hover(element.id, 'section')"
                        @mouseleave="selectionUseCase.dehover()"
                        @click.stop="selectionUseCase.select(element.id, 'section')"
                        @contextmenu="selectionUseCase.select(element.id, 'section')"
                        :tued-contextmenu="contextmenu.register(element.id, 'section')"
                        :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === element.id, 'tued-element-hovered': selectionStore.hovered.id === element.id }"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selectionUseCase.select(element.id, 'section')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('section') }}</span>
                    </div>
                    <!--
                        @draggable-change="sections.update()"
                    -->
                    <Rows
                        :parent="element.id"
                        @draggable-start="startDraggable"
                        @draggable-change="changeDraggable"
                        @draggable-end="endDraggable"
                        @selected="emit('selected')"
                    ></Rows>
                </div>
            </template>
        </vuedraggable>
        <div class="tued-structure-new-element" @click="blockPicker.new()">
            {{ translator.trans('newBlock') }}
        </div>
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import Rows from "admin/Sidebar/Rows.vue";
import { inject, defineEmits, onMounted } from "vue";

const emit = defineEmits(['selected']);
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const sectionsUseCase = inject('usecase.sections');
const rowsUseCase = inject('usecase.rows');
const selectionUseCase = inject('usecase.selection');
const draggableUseCase = inject('usecase.draggable');
const structureStore = inject('structure.store');
const selectionStore = inject('selection.store');
const contextmenu = inject('usecase.contextmenu');
const blockPicker = inject('blocks.picker');

const startDraggable = event => draggableUseCase.start(event);
const changeDraggable = event => draggableUseCase.change(event);
const endDraggable = event => draggableUseCase.end(event);

onMounted(() => {
    contextmenu.items('sections', 'section', () => {
        return [
            {
                group: 'section',
                onClick: (id) => rowsUseCase.newOne(id),
                label: translator.trans('addRow'),
                icon: 'fas fa-plus',
            },
            {
                group: 'section',
                onClick: (id) => sectionsUseCase.newOneAfter(id),
                label: translator.trans('addSectionBelow'),
                icon: 'fas fa-plus',
            },
            {
                group: 'section',
                onClick: (id) => sectionsUseCase.remove(id),
                label: translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            },
        ];
    });
});
</script>

