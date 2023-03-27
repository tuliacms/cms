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
            @change="sectionsUseCase.update()"
            @start="startDraggable"
            @end="endDraggable"
        >
            <template #item="{element}">
                <div
                    class="tued-structure-element tued-structure-element-section"
                    data-element-type="section"
                    :data-element-id="element.id"
                >
                    <!--
                    @dblclick.stop="emits('selected')"
                    :tued-contextmenu="contextmenu.register('section', element.id)"
                    -->
                    <div
                        @mouseenter="selectionUseCase.hover(element.id, 'section')"
                        @mouseleave="selectionUseCase.dehover()"
                        @click.stop="selectionUseCase.select(element.id, 'section')"
                        :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === element.id, 'tued-element-hovered': selectionStore.hovered.id === element.id }"
                    >
                        <div class="tued-structure-draggable-handler" mousedown.stop="selection.select(section, element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('section') }} {{ element.id }}</span>
                    </div>
<!--                    <Rows
                        :parent="element"
                        :rows="element.rows"
                        @draggable-change="sections.update()"
                        @selected="emits('selected')"
                    ></Rows>-->
                </div>
            </template>
        </vuedraggable>
        {{ selectionStore }}
        <div class="tued-structure-new-element" @click="sectionsUseCase.newOne()">
            New section
        </div>
<!--        <div class="tued-structure-new-element" @click="blockPicker.new()">
            {{ translator.trans('newBlock') }}
        </div>-->
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import { inject, defineEmits } from "vue";

const emits = defineEmits(['selected']);
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const sectionsUseCase = inject('usecase.sections');
const selectionUseCase = inject('usecase.selection');
const structureStore = inject('structure.store');
const selectionStore = inject('selection.store');

const startDraggable = () => {
    selectionUseCase.disableSelection();
    selectionUseCase.disableHovering();
};
const endDraggable = (event) => {
    selectionUseCase.enableSelection();
    selectionUseCase.enableHovering();
    selectionUseCase.select(event.item.dataset.elementId, event.item.dataset.elementType);
    selectionUseCase.hover(event.item.dataset.elementId, event.item.dataset.elementType);
};

/*const props = defineProps(['structure']);
const blockPicker = inject('blocks.picker');
const messenger = inject('messenger');
const selection = inject('selection');
const structureManipulator = inject('structureManipulator');
const blocksPicker = inject('blocks.picker');
const contextmenu = inject('contextmenu');*/


/*
onMounted(() => {
    messenger.operation('structure.create.block', (data, success, fail) => {
        if (data && data.columnId) {
            blocksPicker.newAt(data.columnId);
        } else {
            blocksPicker.new();
        }

        success();
    });

    contextmenu.items('sections', 'section', () => {
        return [
            {
                group: 'section',
                onClick: (id) => structureManipulator.newRow(id),
                label: translator.trans('addRow'),
                icon: 'fas fa-plus',
            },
            {
                group: 'section',
                onClick: (id) => structureManipulator.newSection(id),
                label: translator.trans('addSectionBelow'),
                icon: 'fas fa-plus',
            },
            {
                group: 'section',
                onClick: (id) => structureManipulator.removeElement(id),
                label: translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            },
        ];
    });
});*/
</script>

