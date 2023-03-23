<template>
    <div class="tued-sidebar-structure">
        <!--
        @start="handleStart"
        @change="handleChange"
        @end="sendDelta"
        -->
        <draggable
            group="sections"
            item-key="id"
            :list="structure.sections"
            tag="div"
            :component-data="{ name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': '' }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-section > .tued-label > .tued-structure-draggable-handler"
        >
            <template #item="{element}">
                <div class="tued-structure-element tued-structure-element-section">
                    <!--
                    @mouseenter="selection.hover('section', element.id, 'sidebar')"
                    @mouseleave="selection.resetHovered()"
                    @click.stop="selection.select('section', element.id, 'sidebar')"
                    @dblclick.stop="emits('selected')"
                    :tued-contextmenu="contextmenu.register('section', element.id)"
                    -->
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': false, 'tued-element-hovered': false }"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select(section, element.id, 'sidebar')">
                            <i class="fas fa-arrows-alt"></i>
                        </div>
                        <span>{{ translator.trans('section') }}</span>
                    </div>
<!--                    <Rows
                        :parent="element"
                        :rows="element.rows"
                        @draggable-start="handleStart"
                        @draggable-change="handleChange"
                        @draggable-end="sendDelta"
                        @selected="emits('selected')"
                    ></Rows>-->
                </div>
            </template>
        </draggable>
        <div class="tued-structure-new-element" @click="sections.newOne()">
            New section
        </div>
<!--        <div class="tued-structure-new-element" @click="blockPicker.new()">
            {{ translator.trans('newBlock') }}
        </div>-->
    </div>
</template>

<script setup>
const draggable = require('vuedraggable');
/*const Rows = require('components/Admin/Sidebar/Rows.vue').default;*/
/*const DraggableDeltaTranslator = require('shared/Structure/DraggableDeltaTranslator.js').default;*/
const { inject, defineProps, onMounted, defineEmits, computed } = require('vue');

const emits = defineEmits(['selected']);
const structureDragOptions = inject('structureDragOptions');
const translator = inject('translator');
const sections = inject('usecase.sections');
/*const props = defineProps(['structure']);
const blockPicker = inject('blocks.picker');
const messenger = inject('messenger');
const selection = inject('selection');
const structureManipulator = inject('structureManipulator');
const blocksPicker = inject('blocks.picker');
const contextmenu = inject('contextmenu');
let draggableDeltaTranslator = null;*/

const structure = inject('structure');

/*const handleStart = (event) => {
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

