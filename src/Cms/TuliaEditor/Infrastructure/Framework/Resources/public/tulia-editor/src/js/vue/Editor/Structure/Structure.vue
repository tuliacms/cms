<template>
    <div class="tued-structure" ref="structureContainer">
        <Section
            v-for="section in structure.sections"
            :key="section.id"
            :section="section"
            @selection-enter="(id, type) => selectionEnter(id, type)"
            @selection-leave="(id, type) => selectionLeave(id, type)"
        ></Section>

<!--        <div class="tued-structure-new-element" @click="newBlock()">
            {{ translator.trans('newBlock') }}
        </div>-->

        <div
            class="tued-element-boundaries tued-element-selected-boundaries"
            :style="{
                left: selection.selected.boundaries.left + 'px',
                top: selection.selected.boundaries.top + 'px',
                width: selection.selected.boundaries.width + 'px',
                height: selection.selected.boundaries.height + 'px',
            }"
        >
            <div class="tued-node-name">{{ selection.selected.tagName }}</div>
        </div>
        <div
            class="tued-element-boundaries tued-element-hovered-boundaries"
            :style="{
                left: selection.hovered.boundaries.left + 'px',
                top: selection.hovered.boundaries.top + 'px',
                width: selection.hovered.boundaries.width + 'px',
                height: selection.hovered.boundaries.height + 'px',
            }"
        >
        </div>
        <!--<div
            class="tued-element-actions"
            ref="element-actions"
            :style="{
                width: actions.style.width + 'px',
                left: actions.style.left + 'px',
                top: actions.style.top + 'px',
            }"
        >
            <div
                class="tued-element-action"
                :title="translator.trans('selectParentElement')"
                @click="selectParentSelectable()"
                v-if="actions.activeness.selectParent"
            ><i class="fas fa-long-arrow-alt-up"></i></div>
            <div
                class="tued-element-action"
                :title="translator.trans('delete')"
                @click="deleteSelectedElement()"
                v-if="actions.activeness.delete"
            ><i class="fas fa-trash"></i></div>
        </div>-->
    </div>
</template>

<script setup>
import Section from "editor/Structure/Section.vue";
import { inject, ref } from "vue";

const selection = inject('selection.store');
const structure = inject('structure.store');
const structureContainer = ref(null);

const findNode = function (id, type) {
    let node = structureContainer.value.querySelector(`#tued-structure-${type}-${id}`);
    let interval;

    if (!node) {
        interval = setInterval(function() {
            node = structureContainer.value.querySelector(`#tued-structure-${type}-${id}`);

            if (node) {
                clearInterval(interval);
            }
        }, 10);
    }

    return node;
};

inject('selection.selectedElementBoundaries').registerHtmlNodeFinder((id, type) => findNode(id, type));
inject('selection.hoveredElementBoundaries').registerHtmlNodeFinder((id, type) => findNode(id, type));

const hoverResolver = inject('selection.hoveredElementResolver');

const selectionEnter = (id, type) => hoverResolver.enter(id, type);
const selectionLeave = () => hoverResolver.leave();

/*let elm = this.$refs['element-actions'];

    this.actions.style.top = style.top - elm.offsetHeight;
    this.actions.style.left = style.left;
    this.actions.style.width = style.width;*/





/*const Section = require('components/Editor/Structure/Section.vue').default;
const SelectedBoundaries = require('shared/Structure/Selection/Boundaries/Selected.js').default;
const HoveredBoundaries = require('shared/Structure/Selection/Boundaries/Hovered.js').default;
const HoverResolver = require('shared/Structure/Selection/HoverResolver.js').default;
const { toRaw } = require('vue');

export default {
    props: ['structure'],
    inject: ['messenger', 'selection', 'structureManipulator', 'translator'],
    components: { Section },
    data () {
        return {
            width: '100%',
            hoverable: {
                resolver: new HoverResolver(this.selection),
                boundaries: new HoveredBoundaries(this.updateHoverableStyle),
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                }
            },
            selectable: {
                boundaries: new SelectedBoundaries(this.updateSelectableStyle),
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                    tagName: 'div',
                }
            },
            actions: {
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                },
                activeness: {
                    selectParent: false,
                    delete: true,
                }
            }
        }
    },
    methods: {
        selectionEnter: function (type, id) {
            let el = this.getElement(type, id);
            this.hoverable.resolver.enter(el, type, id);
        },
        selectionLeave: function () {
            this.hoverable.resolver.leave();
        },
        updateHoverableStyle: function (style) {
            this.hoverable.style.left = style.left;
            this.hoverable.style.top = style.top;
            this.hoverable.style.width = style.width;
            this.hoverable.style.height = style.height;
        },
        updateSelectableStyle: function (style) {
            this.selectable.style.left = style.left;
            this.selectable.style.top = style.top;
            this.selectable.style.width = style.width;
            this.selectable.style.height = style.height;
            this.selectable.style.tagName = style.tagName;

            let elm = this.$refs['element-actions'];

            this.actions.style.top = style.top - elm.offsetHeight;
            this.actions.style.left = style.left;
            this.actions.style.width = style.width;
        },
        selectParentSelectable: function () {
            let selected = this.selection.getSelected();

            if (!selected) {
                return;
            }

            let parent = this.structureManipulator.findParent(selected.id);

            if (!parent) {
                return;
            }

            this.selection.select(parent.type, parent.id, 'editor');
        },
        deleteSelectedElement: function () {
            let selected = this.selection.getSelected();

            if (!selected) {
                console.error('Cannot remove selected element. None of elements were selected.');
                return;
            }

            this.structureManipulator.removeElement(selected.id);
        },
        hideElementActions: function () {
            this.actions.activeness.selectParent = false;
        },
        updateElementActions: function (el, type, element) {
            let parent = this.structureManipulator.findParent(element.id);

            if (!parent) {
                this.hideElementActions();
                return;
            }

            this.actions.activeness.selectParent = true;
        },
        getElement: function (type, id) {
            return this.$refs['structure'].querySelector(`#tued-structure-${type}-${id}`);
        },
        newBlock: function () {
            this.messenger.execute('structure.create.block');
        }
    },
    mounted () {
        this.messenger.on('structure.element.updated', () => {
            this.hoverable.boundaries.update();
            this.selectable.boundaries.update();
        });
        this.messenger.on('structure.element.removed', () => {
            this.selection.resetSelection();
            this.selection.resetHovered();
        });

        /!**
         * Selection
         *!/
        this.messenger.on('structure.selection.select', (type, id) => {
            let node = this.getElement(type, id);
            let element = this.structureManipulator.find(id);

            this.selectable.boundaries.highlightSelected(node);
            this.updateElementActions(node, type, element);
        });
        this.messenger.on('structure.selection.deselect', () => {
            this.selectable.boundaries.clearSelectionHighlight();
        });
        this.messenger.on('canvas.view.updated', () => {
            this.selectable.boundaries.update();
            this.hoverable.boundaries.update();
        });
        this.messenger.on('structure.selection.hover', (type, id) => {
            let node = this.getElement(type, id);

            this.hoverable.boundaries.highlight(node, type, id);
        });
        this.messenger.on('structure.selection.dehover', () => {
            this.hoverable.boundaries.clear();
        });
        this.messenger.on('device.size.changed', () => {
            let animationTime = 300;
            this.selectable.boundaries.keepUpdatePositionFor(animationTime);
        });
    }
};*/
</script>
