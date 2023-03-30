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
                <div class="tued-structure-element tued-structure-element-row">
                    <!--
                    @dblclick.stop="$emit('selected')"
                    -->
                    <div
                        @mouseenter="selectionUseCase.hover(element.id, 'row')"
                        @mouseleave="selectionUseCase.dehover()"
                        @click.stop="selectionUseCase.select(element.id, 'row')"
                        @contextmenu="selectionUseCase.select(element.id, 'row')"
                        :tued-contextmenu="contextmenu.register('row', element.id)"
                        :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === element.id, 'tued-element-hovered': selectionStore.hovered.id === element.id }"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selectionUseCase.select(element.id, 'row')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('row') }} {{ element.id }}</span>
                    </div>
<!--                    <Columns
                        :parent="element"
                        :columns="element.columns"
                        @draggable-start="(event) => $emit('draggable-start', event)"
                        @draggable-change="(event) => $emit('draggable-change', event)"
                        @draggable-end="(event) => $emit('draggable-end', event)"
                        @selected="$emit('selected')"
                    ></Columns>-->
                </div>
            </template>
        </vuedraggable>
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import { inject, defineEmits, defineProps } from "vue";

const props = defineProps(['parent']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end']);
const translator = inject('translator');
const structureDragOptions = inject('structureDragOptions');
const selectionUseCase = inject('usecase.selection');
const selectionStore = inject('selection.store');
const structureStore = inject('structure.store');
const contextmenu = inject('usecase.contextmenu');
</script>

<!--<script>
const draggable = require('vuedraggable');
const Columns = require('components/Admin/Sidebar/Columns.vue').default;

export default {
    props: ['parent', 'rows'],
    inject: ['selection', 'structureDragOptions', 'structureManipulator', 'translator', 'contextmenu'],
    components: {
        draggable,
        Columns
    },
    mounted() {
        this.contextmenu.items('rows', 'row', (id) => {
            const row = this.structureManipulator.find(id);
            const items = [];

            items.push({
                group: 'row',
                onClick: (id) => this.structureManipulator.newColumn(id),
                label: this.translator.trans('addColumn'),
                icon: 'fas fa-plus',
            });

            items.push({
                group: 'row',
                onClick: (id) => this.structureManipulator.removeElement(id),
                label: this.translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            });

            return items;
        });
    }
};
</script>-->
