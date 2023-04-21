<template>
    <div>
        <vuedraggable
            group="columns"
            item-key="id"
            :list="structureStore.columnsOf(props.parent)"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `row.${parent}` }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-column > .tued-label > .tued-structure-draggable-handler"
            @start="(event) => emit('draggable-start', event)"
            @change="(event) => emit('draggable-change', event)"
            @end="(event) => emit('draggable-end', event)"
        >
            <template #item="{element}">
                <Column
                    data-element-type="column"
                    :data-element-id="element.id"
                    :column="element"
                    @selected="emit('selected')"
                    @draggable-start="(event) => emit('draggable-start', event)"
                    @draggable-change="(event) => emit('draggable-change', event)"
                    @draggable-end="(event) => emit('draggable-end', event)"
                ></Column>
            </template>
        </vuedraggable>
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import Column from "admin/Sidebar/Column.vue";
import { inject, defineEmits, defineProps, onMounted, computed } from "vue";
import Blocks from "admin/Sidebar/Blocks.vue";

const props = defineProps(['parent']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end', 'selected']);
const translator = inject('translator');
const structureDragOptions = inject('structureDragOptions');
const columnsUseCase = inject('usecase.columns');
const structureStore = inject('structure.store');
const contextmenu = inject('usecase.contextmenu');

const columnRefs = {};

onMounted(() => {
    for (let i in props.columns) {
        columnRefs['column-' + props.columns[i].id] = ref('column-' + props.columns[i].id);
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
</script>
