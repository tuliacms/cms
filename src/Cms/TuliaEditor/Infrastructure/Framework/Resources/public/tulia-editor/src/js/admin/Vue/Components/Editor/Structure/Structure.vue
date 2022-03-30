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
import Section from './Section.vue';
import Structure from '../../../../../shared/Structure.js';

class Hoverable {
    hoveredElement;
    hoveredStack = [];
    emitter;
    disabledStack = 0;

    constructor (emitter) {
        this.emitter = emitter;
    }

    enter (element, meta) {
        this.hoveredElement = {
            element: element,
            meta: meta
        };
        this.hoveredStack.push(this.hoveredElement);
        this.updatePosition();
    }

    leave () {
        this.hoveredStack.pop();

        if (this.hoveredStack[this.hoveredStack.length - 1]) {
            this.hoveredElement = this.hoveredStack[this.hoveredStack.length - 1];
            this.updatePosition();
        } else {
            this.resetPosition();
        }
    }

    hide () {
        this.hoveredStack = [];
        this.hoveredElement = null;
        this.resetPosition();
    }

    update () {
        this.updatePosition();
    }

    disable () {
        this.disabledStack++;

        if (this.isDisabled()) {
            this.resetPosition();
        }
    }

    isDisabled () {
        return this.disabledStack > 0;
    }

    enable () {
        this.disabledStack--;

        if (this.isDisabled() === false) {
            this.updatePosition();
        }
    }

    updatePosition () {
        if (this.isDisabled()) {
            return;
        }

        if (!this.hoveredElement) {
            return;
        }

        this.emit('structure.hovering.hover', {
            style: {
                top: this.hoveredElement.element.offsetTop,
                left: this.hoveredElement.element.offsetLeft,
                width: this.hoveredElement.element.offsetWidth,
                height: this.hoveredElement.element.offsetHeight,
            },
            meta: this.hoveredElement.meta,
        });
    }

    resetPosition () {
        this.emit('structure.hovering.clear', {
            style: {
                top: -100,
                left: -100,
                width: 0,
                height: 0,
            }
        });
    }

    emit (event, ...args) {
        this.emitter(event, ...args);
    }
}

class Selectable {
    selectedElement;
    positionUpdateAnimationFrameHandle;
    emitter;

    constructor (emitter) {
        this.emitter = emitter;
    }

    select (element, meta) {
        this.selectedElement = {
            element: element,
            meta: meta
        };
        this.emit('structure.selection.selected', element, meta);
        this.updatePosition();
    }

    clearSelection () {
        this.selectedElement = null;
        this.resetPosition();
    }

    hide () {
        this.resetPosition();
    }

    show () {
        this.updatePosition();
    }

    update () {
        this.updatePosition();
    }

    keepUpdatePositionFor (microseconds) {
        let self = this;

        function doTheUdate() {
            self.updatePosition();
            self.positionUpdateAnimationFrameHandle = requestAnimationFrame(doTheUdate);
        }

        requestAnimationFrame(doTheUdate);

        setTimeout(() => {
            cancelAnimationFrame(self.positionUpdateAnimationFrameHandle);
        }, microseconds);
    }

    getSelectedElement () {
        return this.selectedElement && this.selectedElement.element
            ? this.selectedElement.element
            : null;
    }

    getSelectedMeta () {
        return this.selectedElement && this.selectedElement.meta
            ? this.selectedElement.meta
            : null;
    }

    updatePosition () {
        if (!this.selectedElement) {
            return;
        }

        let doc = this.selectedElement.element.ownerDocument;

        this.emit('structure.selection.update-view', {
            top: this.selectedElement.element.offsetTop,
            left: this.selectedElement.element.offsetLeft,
            width: this.selectedElement.element.offsetWidth,
            height: this.selectedElement.element.offsetHeight,
            tagName: this.selectedElement.element.dataset.tagname ?? this.selectedElement.element.tagName,
            // @todo Remove dependency to jQuery
            scrollTop : $(doc.defaultView || doc.parentWindow).scrollTop()
        });
    }

    resetPosition () {
        this.emit('structure.selection.update-view', {
            top: -100,
            left: -100,
            width: 0,
            height: 0,
            tagName: null,
            scrollTop : 0
        });
    }

    emit (event, ...args) {
        this.emitter(event, ...args);
    }
}

export default {
    props: ['structure', 'messenger'],
    components: { Section },
    data () {
        return {
            width: '100%',
            hoverable: {
                manager: new Hoverable((...args) => {this.$root.$emit(...args)}),
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                }
            },
            selectable: {
                manager: new Selectable((...args) => {this.$root.$emit(...args)}),
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
            let meta = this.selectable.manager.getSelectedMeta();

            if (!meta) {
                return;
            }

            let parent = Structure.findParent(this.structure, meta.id);

            if (!parent) {
                return;
            }

            this.selectable.manager.select(this.getElement(parent.type, parent.id), {
                type: parent.type,
                id: parent.id,
            });
        },
        deleteSelectedElement: function () {
            let element = this.selectable.manager.getSelectedMeta();
            let found = false;

            if (!element) {
                console.error('Cannot remove selected element. None of elements were selected.');
                return;
            }

            element = Structure.find(this.structure, element.id);
            parent = Structure.findParent(this.structure, element.id);

            if (element.type === 'block') {
                let index = parent.blocks.indexOf(element);

                if (index >= 0) {
                    parent.blocks.splice(index, 1);
                    this.selectable.manager.clearSelection();
                    this.messenger.send('structure.block.removed', element);
                    found = true;
                }
            } else if (element.type === 'column') {
                let index = parent.columns.indexOf(element);

                if (index >= 0) {
                    parent.columns.splice(index, 1);
                    this.selectable.manager.clearSelection();
                    this.messenger.send('structure.column.removed', element);
                    found = true;
                }
            } else if (element.type === 'row') {
                let index = parent.rows.indexOf(element);

                if (index >= 0) {
                    parent.rows.splice(index, 1);
                    this.selectable.manager.clearSelection();
                    this.messenger.send('structure.row.removed', element);
                    found = true;
                }
            } else if (element.type === 'section') {
                let index = this.structure.sections.indexOf(element);

                if (index >= 0) {
                    this.structure.sections.splice(index, 1);
                    this.selectable.manager.clearSelection();
                    this.messenger.send('structure.section.removed', element);
                    found = true;
                }
            }

            if (found) {
                this.messenger.send('structure.synchronize.from.editor', this.structure);
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
        /**
         * Hovering
         */
        this.$root.$on('structure.hovering.enter', (type, id) => {
            this.messenger.send('structure.hovering.enter', type, id);
        });
        this.$root.$on('structure.hovering.leave', (type, id) => {
            this.messenger.send('structure.hovering.leave', type, id);
        });
        this.$root.$on('structure.hovering.clear', (data) => {
            this.messenger.send('structure.hovering.clear');
            this.updateHoverableStyle(data.style);
        });
        this.$root.$on('structure.hovering.hover', (data) => {
            this.messenger.send('structure.hovering.hover', data.meta.type, data.meta.id);
            this.updateHoverableStyle(data.style);
        });

        /**
         * Selection
         */
        this.$root.$on('structure.selection.select', (type, id) => {
            this.messenger.send('structure.selection.select', type, id);
        });
        this.$root.$on('structure.selection.selected', (element, meta) => {
            this.messenger.send('structure.selection.selected', meta.type, meta.id);
        });
        this.$root.$on('structure.selection.hide', () => {
            this.selectable.manager.hide();
        });
        this.$root.$on('structure.selection.show', () => {
            this.selectable.manager.show();
        });
        this.$root.$on('structure.selection.cleared', () => {
            this.messenger.send('structure.selection.deselected');
        });
        this.$root.$on('structure.selection.update-view', (style) => {
            this.updateSelectableStyle(style);
        });

        /**
         * Common
         */
        this.$root.$on('block.inner.updated', () => {
            this.hoverable.manager.update();
            this.selectable.manager.update();
        });
        this.$root.$on('editor.save', () => {
            this.selectable.manager.hide();
            this.hoverable.manager.hide();
        });




        this.messenger.listen('editor.cancel', () => {
            this.selectable.manager.hide();
            this.hoverable.manager.hide();
        });
        this.messenger.listen('device.size.changed', () => {
            let animationTime = 300;
            this.selectable.manager.keepUpdatePositionFor(animationTime);
        });
        this.messenger.listen('structure.move-element', (delta) => {
            Structure.moveElementUsingDelta(this.structure, delta);
            this.hoverable.manager.hide();
            this.messenger.send('structure.selection.select', delta.element.type, delta.element.id);
        });
        this.messenger.listen('structure.updated', () => {
            this.hoverable.manager.update();
            this.selectable.manager.update();
        });




        /**
         * Hovering
         */
        this.messenger.listen('structure.hovering.enter', (type, id) => {
            let el = this.getElement(type, id);
            this.hoverable.manager.enter(el, {
                type: type,
                id: id
            });
        });
        this.messenger.listen('structure.hovering.leave', (type, id) => {
            let el = this.getElement(type, id);
            this.hoverable.manager.leave(el, {
                type: type,
                id: id
            });
        });
        this.messenger.listen('strucure.hovering.disable', () => {
            this.hoverable.manager.disable();
        });
        this.messenger.listen('strucure.hovering.enable', () => {
            this.hoverable.manager.enable();
        });

        /**
         * Selection
         */
        this.messenger.listen('structure.selection.deselected', () => {
            this.selectable.manager.clearSelection();
            this.selectable.manager.hide();
            this.hoverable.manager.hide();
        });
        this.messenger.listen('structure.selection.select', (type, id) => {
            let element = this.getElement(type, id);

            this.selectable.manager.select(element, {
                type: type,
                id: id
            });
        });
        this.messenger.listen('structure.selection.selected', (type, id) => {
            let element = Structure.find(this.structure, id);
            let el = this.getElement(type, id);

            this.updateElementActions(el, type, element);
        });
    }
};
</script>
