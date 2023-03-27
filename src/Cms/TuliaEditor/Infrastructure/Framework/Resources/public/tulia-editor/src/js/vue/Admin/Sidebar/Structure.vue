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
                    -->
                    <div
                        @mouseenter="selectionUseCase.hover(element.id, 'section')"
                        @mouseleave="selectionUseCase.dehover()"
                        @click.stop="selectionUseCase.select(element.id, 'section')"
                        @contextmenu="selectionUseCase.select(element.id, 'section')"
                        :tued-contextmenu="contextmenu.register('section', element.id)"
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
import { inject, defineEmits, onMounted } from "vue";

const emits = defineEmits(['selected']);
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const sectionsUseCase = inject('usecase.sections');
const selectionUseCase = inject('usecase.selection');
const draggableUseCase = inject('usecase.draggable');
const structureStore = inject('structure.store');
const selectionStore = inject('selection.store');
const contextmenu = inject('usecase.contextmenu');

const startDraggable = (event) => draggableUseCase.start(event.item.dataset.elementId, event.item.dataset.elementType);
const endDraggable = (event) => draggableUseCase.end(event.item.dataset.elementId, event.item.dataset.elementType);

/*const props = defineProps(['structure']);
const blockPicker = inject('blocks.picker');
const messenger = inject('messenger');
const selection = inject('selection');
const structureManipulator = inject('structureManipulator');
const blocksPicker = inject('blocks.picker');*/



onMounted(() => {
    /*messenger.operation('structure.create.block', (data, success, fail) => {
        if (data && data.columnId) {
            blocksPicker.newAt(data.columnId);
        } else {
            blocksPicker.new();
        }

        success();
    });*/

    contextmenu.items('sections', 'section', () => {
        return [
           /* {
                group: 'section',
                onClick: (id) => structureManipulator.newRow(id),
                label: translator.trans('addRow'),
                icon: 'fas fa-plus',
            },*/
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

