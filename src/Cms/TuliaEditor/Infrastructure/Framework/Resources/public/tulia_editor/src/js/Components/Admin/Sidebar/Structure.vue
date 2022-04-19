<template>
    <div class="tued-structure">
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
                        @click.stop="selection.select('section', element.id)"
                        @mouseenter="selection.hover('section', element.id)"
                        @mouseleave="selection.resetHovered()"
                    >
                        <div class="tued-structure-draggable-handler" @mousedown.stop="selection.select(section, element.id)">
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
        <div class="tued-structure-new-element ml-0" @click="structureManipulator.newSection()">
            {{ translator.trans('newSection') }}
        </div>
    </div>
</template>

<script>
const draggable = require('vuedraggable');
const Rows = require('components/Admin/Sidebar/Rows.vue').default;
const DraggableDeltaTranslator = require('shared/Structure/DraggableDeltaTranslator.js').default;

export default {
    props: ['structure'],
    inject: [
        'eventDispatcher',
        'messenger',
        'selection',
        'structureDragOptions',
        'structureManipulator',
        'translator'
    ],
    components: {
        draggable,
        Rows
    },
    data () {
        return {
            draggableDeltaTranslator: null
        }
    },
    methods: {
        handleStart: function (event) {
            this.draggableDeltaTranslator = new DraggableDeltaTranslator(event);

            this.selection.resetHovered();
            this.selection.disableHovering();
        },
        handleChange: function (change) {
            this.draggableDeltaTranslator.handle(change);
        },
        sendDelta: function (event) {
            this.selection.enableHovering();

            let delta = this.draggableDeltaTranslator.stop(event);

            if (!delta) {
                return;
            }

            this.messenger.notify('structure.move-element', delta);
        }
    }
};
</script>
