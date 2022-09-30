<template>
    <div>
        <draggable
            group="rows"
            :list="rows"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-row > .tued-label > .tued-structure-draggable-handler"
            item-key="id"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            @start="(event) => $emit('draggable-start', event)"
            @change="(event) => $emit('draggable-change', event)"
            @end="(event) => $emit('draggable-end', event)"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-row">
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': element.metadata.selected, 'tued-element-hovered': element.metadata.hovered }"
                        @click.stop="selection.select('row', element.id, 'sidebar')"
                        @mouseenter="selection.hover('row', element.id, 'sidebar')"
                        @mouseleave="selection.resetHovered()"
                        :tued-contextmenu="contextmenu.register('row', element.id)"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('row', element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('row') }}</span>
                    </div>
                    <Columns
                        :parent="element"
                        :columns="element.columns"
                        @draggable-start="(event) => $emit('draggable-start', event)"
                        @draggable-change="(event) => $emit('draggable-change', event)"
                        @draggable-end="(event) => $emit('draggable-end', event)"
                    ></Columns>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script>
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
</script>
