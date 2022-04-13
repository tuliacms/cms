<template>
    <div
        class="tued-structure"
        ref="structure"
    >
        <Section
            v-for="section in structure.sections"
            :id="'tued-structure-section-' + section.id"
            :key="section.id"
            :section="section"
            @selection-enter="(type, id) => selectionEnter(type, id)"
            @selection-leave="(type, id) => selectionLeave(type, id)"
        ></Section>
        <div
            class="tued-element-boundaries tued-element-selected-boundaries"
            :style="{
                left: selectable.style.left + 'px',
                top: selectable.style.top + 'px',
                width: selectable.style.width + 'px',
                height: selectable.style.height + 'px',
            }"
        >
            <div class="tued-node-name">{{ selectable.style.tagName }}</div>
        </div>
        <div
            class="tued-element-boundaries tued-element-hovered-boundaries"
            :style="{
                left: hoverable.style.left + 'px',
                top: hoverable.style.top + 'px',
                width: hoverable.style.width + 'px',
                height: hoverable.style.height + 'px',
            }"
        >
        </div>
        <div
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
                title="Zaznacz blok wyżej"
                @click="selectParentSelectable()"
                v-if="actions.activeness.selectParent"
            ><i class="fas fa-long-arrow-alt-up"></i></div>
            <div
                class="tued-element-action"
                title="Duplikuj"
                v-if="actions.activeness.duplicate"
            ><i class="fas fa-copy"></i></div>
            <div
                class="tued-element-action"
                title="Usuń"
                @click="deleteSelectedElement()"
                v-if="actions.activeness.delete"
            ><i class="fas fa-trash"></i></div>
        </div>
    </div>
</template>

<script>
const Section = require('components/Editor/Structure/Section.vue').default;
const Structure = require('shared/Structure.js').default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const SelectedBoundaries = require('shared/Structure/Selection/Boundaries/Selected.js').default;
const HoveredBoundaries = require('shared/Structure/Selection/Boundaries/Hovered.js').default;
const HoverResolver = require('shared/Structure/Selection/HoverResolver.js').default;
const { toRaw } = require('vue');

export default {
    props: ['structure'],
    inject: ['messenger', 'selection', 'eventDispatcher'],
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
                    duplicate: true,
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

            let parent = Structure.findParent(this.structure, selected.id);

            if (!parent) {
                return;
            }

            this.selection.select(parent.type, parent.id);
        },
        deleteSelectedElement: function () {
            let selected = this.selection.getSelected();
            let found = false;

            if (!selected) {
                console.error('Cannot remove selected element. None of elements were selected.');
                return;
            }

            let element = Structure.find(this.structure, selected.id);
            let parent = Structure.findParent(this.structure, element.id);

            if (element.type === 'block') {
                let index = parent.blocks.indexOf(element);

                if (index >= 0) {
                    parent.blocks.splice(index, 1);
                    this.selectable.boundaries.clearSelectionHighlight();
                    this.messenger.send('structure.block.removed', ObjectCloner.deepClone(toRaw(element)));
                    found = true;
                }
            } else if (element.type === 'column') {
                let index = parent.columns.indexOf(element);

                if (index >= 0) {
                    parent.columns.splice(index, 1);
                    this.selectable.boundaries.clearSelectionHighlight();
                    this.messenger.send('structure.column.removed', ObjectCloner.deepClone(toRaw(element)));
                    found = true;
                }
            } else if (element.type === 'row') {
                let index = parent.rows.indexOf(element);

                if (index >= 0) {
                    parent.rows.splice(index, 1);
                    this.selectable.boundaries.clearSelectionHighlight();
                    this.messenger.send('structure.row.removed', ObjectCloner.deepClone(toRaw(element)));
                    found = true;
                }
            } else if (element.type === 'section') {
                let index = this.structure.sections.indexOf(element);

                if (index >= 0) {
                    this.structure.sections.splice(index, 1);
                    this.selectable.boundaries.clearSelectionHighlight();
                    this.messenger.send('structure.section.removed', ObjectCloner.deepClone(toRaw(element)));
                    found = true;
                }
            }

            if (found) {
                this.messenger.send('structure.synchronize.from.editor', ObjectCloner.deepClone(toRaw(this.structure)));
                this.selection.resetSelection();
                this.selection.resetHovered();
            }
        },
        hideElementActions: function () {
            this.actions.activeness.selectParent = false;
        },
        updateElementActions: function (el, type, element) {
            let parent = Structure.findParent(this.structure, element.id);

            if (!parent) {
                this.hideElementActions();
                return;
            }

            this.actions.activeness.selectParent = true;
        },
        getElement: function (type, id) {
            return this.$refs['structure'].querySelector(`#tued-structure-${type}-${id}`);
        }
    },
    mounted () {
        this.eventDispatcher.on('block.inner.updated', () => {
            this.hoverable.boundaries.update();
            this.selectable.boundaries.update();
        });


        /*this.messenger.listen('device.size.changed', () => {
            let animationTime = 300;
            this.selectable.boundaries.keepUpdatePositionFor(animationTime);
        });*/


        /**
         * Selection
         */
        this.messenger.listen('structure.selection.select', (type, id) => {
            let node = this.getElement(type, id);
            let element = Structure.find(this.structure, id);

            this.selectable.boundaries.highlightSelected(node);
            this.updateElementActions(node, type, element);
        });
        this.messenger.listen('structure.selection.deselect', () => {
            this.selectable.boundaries.clearSelectionHighlight();
        });
        this.messenger.listen('structure.selection.hover', (type, id) => {
            let node = this.getElement(type, id);

            this.hoverable.boundaries.highlight(node, type, id);
        });
        this.messenger.listen('structure.selection.dehover', () => {
            this.hoverable.boundaries.clear();
        });
    }
};
</script>
