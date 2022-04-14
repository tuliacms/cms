<template>
    <div class="tued-structure">
        <draggable
            group="sections"
            item-key="id"
            :list="structure.sections"
            tag="div"
            :component-data="{ name:'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': '' }"
            v-bind="dragOptions"
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
                        <span>Sekcja</span>
                    </div>
                    <Rows
                        :parent="element"
                        :rows="element.rows"
                    ></Rows>
                </div>
            </template>
        </draggable>
    </div>
</template>

<script>
const draggable = require('vuedraggable');
const Rows = require('components/Admin/Sidebar/Rows.vue').default;
const DraggableDeltaTranslator = require('shared/Structure/DraggableDeltaTranslator.js').default;

export default {
    props: ['structure'],
    inject: ['eventDispatcher', 'messenger', 'selection'],
    components: {
        draggable,
        Rows
    },
    data () {
        return {
            dragOptions: {
                animation: 200,
                disabled: false,
                ghostClass: 'tued-structure-draggable-ghost'
            },
            draggableDeltaTranslator: null
        }
    },
    methods: {
        handleStart: function (event) {
            this.eventDispatcher.emit('structure.element.draggable.start', event);
        },
        handleChange: function (change) {
            this.eventDispatcher.emit('structure.element.draggable.change', change);
        },
        sendDelta: function (event) {
            this.eventDispatcher.emit('structure.element.draggable.stop', event);
        }
    },
    mounted () {
        this.eventDispatcher.on('structure.element.draggable.start', (event) => {
            this.draggableDeltaTranslator = new DraggableDeltaTranslator(event);

            this.selection.resetHovered();
            this.selection.disableHovering();
        });
        this.eventDispatcher.on('structure.element.draggable.change', (change) => {
            this.draggableDeltaTranslator.handle(change);
        });
        this.eventDispatcher.on('structure.element.draggable.stop', (event) => {
            this.selection.enableHovering();

            let delta = this.draggableDeltaTranslator.stop(event);

            if (!delta) {
                return;
            }

            this.messenger.send('structure.move-element', delta);
        });
    }
};
</script>
