<template>
    <div class="tued-structure">
        <draggable
            group="sections"
            :list="structure.sections"
            v-bind="dragOptions"
            handle=".tued-structure-draggable-placeholder > .tued-structure-element-section > .tued-label > .tued-structure-draggable-handler"
            @start="handleStart"
            @change="handleChange"
            @end="sendDelta"
        >
            <transition-group
                type="transition"
                class="tued-structure-draggable-placeholder"
                tag="div"
                data-draggable-delta-transformer-parent=""
            >
                <div
                    class="tued-structure-element tued-structure-element-section"
                    v-for="section in structure.sections"
                    :key="'section-' + section.id"
                >
                    <div
                        :class="{ 'tued-label': true, 'tued-element-selected': selected.id === section.id, 'tued-element-hovered': hovered.id === section.id }"
                        @click.stop="$root.$emit('structure.element.select', 'section', section)"
                        @mouseenter="$root.$emit('structure.element.enter', 'section', section)"
                        @mouseleave="$root.$emit('structure.element.leave', 'section', section)"
                    >
                        <div class="tued-structure-draggable-handler"><i class="fas fa-arrows-alt"></i></div>
                        <span>Sekcja</span>
                    </div>
                    <Rows
                        :parent="section"
                        :rows="section.rows"
                        :selected="selected"
                        :hovered="hovered"
                        :messenger="messenger"
                        :canvas="canvas"
                    ></Rows>
                </div>
            </transition-group>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Rows from './Rows.vue';
import DraggableDeltaTranslator from '../../../../../shared/DraggableDeltaTranslator.js';

export default {
    props: ['structure', 'messenger', 'canvas'],
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
            selected: {
                id: null
            },
            hovered: {
                id: null
            },
            draggableTranslator: null
        }
    },
    methods: {
        handleStart: function (event) {
            this.$root.$emit('structure.element.draggable.start', event);
        },
        handleChange: function (change) {
            this.$root.$emit('structure.element.draggable.change', change);
        },
        sendDelta: function (event) {
            this.$root.$emit('structure.element.draggable.stop', event);
        }
    },
    mounted () {
        this.$root.$on('structure.element.select', (type, object) => {
            this.messenger.send('structure.selection.select', type, object.id);
        });
        this.$root.$on('structure.element.enter', (type, object) => {
            this.messenger.send('structure.hovering.enter', type, object.id);
        });
        this.$root.$on('structure.element.leave', (type, object) => {
            this.messenger.send('structure.hovering.leave', type, object.id);
        });
        this.$root.$on('structure.element.draggable.start', (event) => {
            this.draggableTranslator = new DraggableDeltaTranslator(event);
            this.messenger.send('strucure.hovering.disable');
        });
        this.$root.$on('structure.element.draggable.change', (change) => {
            this.draggableTranslator.handle(change);
        });
        this.$root.$on('structure.element.draggable.stop', (event) => {
            this.messenger.send('strucure.hovering.enable');

            let delta = this.draggableTranslator.stop(event);

            if (!delta) {
                return;
            }

            this.messenger.send('structure.move-element', delta);
        });



        this.messenger.listen('structure.selection.selected', (type, id) => {
            this.selected.id = id;
        });
        this.messenger.listen('structure.selection.deselected', () => {
            this.selected.id = null;
        });
        this.messenger.listen('structure.hovering.hover', (type, id) => {
            this.hovered.id = id;
        });
        this.messenger.listen('structure.hovering.clear', () => {
            this.hovered.id = null;
        });
        this.messenger.listen('editor.cancel', () => {
            this.selected.id = null;
            this.hovered.id = null;
        });
    }
};
</script>
