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
                        @click.stop="selection.select('row', element.id)"
                        @mouseenter="selection.hover('row', element.id)"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select('row', element.id)">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>Wiersz</span>
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
    inject: ['selection', 'structureDragOptions'],
    components: {
        draggable,
        Columns
    }
};
</script>
