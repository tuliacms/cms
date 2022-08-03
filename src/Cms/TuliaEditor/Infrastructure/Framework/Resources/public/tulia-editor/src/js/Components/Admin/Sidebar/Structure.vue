<template>
    <div class="tued-sidebar-structure">
        <draggable
            group="sections"
            item-key="id"
            :list="structure.sections"
            tag="div"
            :component-data="{ name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': '' }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-section > .tued-label > .tued-structure-draggable-handler"
            @start="handleStart"
            @change="handleChange"
            @end="sendDelta"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-section">
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': element.metadata.selected, 'tued-element-hovered': element.metadata.hovered }"
                        @click.stop="selection.select('section', element.id, 'sidebar')"
                        @mouseenter="selection.hover('section', element.id, 'sidebar')"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select(section, element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('section') }}</span>
                    </div>
                    <Rows
                        :parent="element"
                        :rows="element.rows"
                        @draggable-start="handleStart"
                        @draggable-change="handleChange"
                        @draggable-end="sendDelta"
                    ></Rows>
                </div>
            </template>
        </draggable>
        <div class="tued-structure-new-element ml-0" @click="blockPicker.new()">
            {{ translator.trans('newBlock') }}
        </div>
    </div>
</template>

<script setup>
const draggable = require('vuedraggable');
const Rows = require('components/Admin/Sidebar/Rows.vue').default;
const DraggableDeltaTranslator = require('shared/Structure/DraggableDeltaTranslator.js').default;
const { inject, defineProps, onMounted } = require('vue');

const props = defineProps(['structure']);
const blockPicker = inject('blocks.picker');
const messenger = inject('messenger');
const selection = inject('selection');
const structureDragOptions = inject('structureDragOptions');
const structureManipulator = inject('structureManipulator');
const translator = inject('translator');
const blocksPicker = inject('blocks.picker');
let draggableDeltaTranslator = null;

const handleStart = (event) => {
    draggableDeltaTranslator = new DraggableDeltaTranslator(event);

    selection.resetHovered();
    selection.disableHovering();
};
const handleChange = (change) => {
    draggableDeltaTranslator.handle(change);
};
const sendDelta = (event) => {
    selection.enableHovering();

    let delta = draggableDeltaTranslator.stop(event);

    if (!delta) {
        return;
    }

    messenger.notify('structure.move-element', delta);
};

onMounted(() => {
    messenger.operation('structure.create.block', (data, success, fail) => {
        if (data && data.columnId) {
            blocksPicker.newAt(data.columnId);
        } else {
            blocksPicker.new();
        }

        success();
    });
});
</script>

