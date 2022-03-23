<template>
    <div
        class="tued-structure"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'root')"
    >
        <Section
            v-for="(section, key) in structure.sections"
            :key="'section-' + key"
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
import Section from "./Section.vue";

class Hoverable {
    hoveredElement;
    hoveredStack = [];
    positionUpdater;

    constructor (positionUpdater) {
        this.positionUpdater = positionUpdater;
    }

    enter (element) {
        this.hoveredStack.push(element);
        this.hoveredElement = element;
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

    updatePosition () {
        if (!this.hoveredElement) {
            return;
        }

        this.positionUpdater({
            top: this.hoveredElement.offsetTop,
            left: this.hoveredElement.offsetLeft,
            width: this.hoveredElement.offsetWidth,
            height: this.hoveredElement.offsetHeight,
        });
    }

    resetPosition () {
        this.positionUpdater({
            top: -100,
            left: -100,
            width: 0,
            height: 0,
        });
    }
}

class Selectable {
    selectedElement;
    positionUpdater;
    positionUpdateAnimationFrameHandle;

    constructor (positionUpdater) {
        this.positionUpdater = positionUpdater;
    }

    select (element) {
        this.selectedElement = element;
        this.updatePosition();
    }

    deselect () {
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
        return this.selectedElement;
    }

    updatePosition () {
        if (!this.selectedElement) {
            return;
        }

        let doc = this.selectedElement.ownerDocument;

        this.positionUpdater({
            top: this.selectedElement.offsetTop,
            left: this.selectedElement.offsetLeft,
            width: this.selectedElement.offsetWidth,
            height: this.selectedElement.offsetHeight,
            tagName: this.selectedElement.dataset.tagname ?? this.selectedElement.tagName,
            scrollTop : $(doc.defaultView || doc.parentWindow).scrollTop()
        });
    }

    resetPosition () {
        this.positionUpdater({
            top: -100,
            left: -100,
            width: 0,
            height: 0,
        });
    }
}

class Selection {
    _emitter = null;
    _rootAchived = true;
    _lastSelectedElement = null;
    _firstElementSelected = false;
    _stack = [];

    constructor(emitter) {
        this._emitter = emitter;
    }

    selectElementUsingStackUntillRoot (type, element) {
        // Prevents double click on canvas and reset the selection.
        if (this._lastSelectedElement === element.el) {
            return;
        }

        this._lastSelectedElement = element.el;
        this.clearIfRootAchived();

        if (type === 'root') {
            this._rootAchived = true;
            // Notice only when stack is complete
            this.noticeWhenFirstElementIsSelected();
        } else {
            this._rootAchived = false;
            this._stack.push(element);
        }
    }

    noticeWhenFirstElementIsSelected() {
        if (this._firstElementSelected === false) {
            this._firstElementSelected = true;
            this.emit('selection.selected', this._stack[0]);
        }
    }

    clearSelection () {
        this._stack = [];
        this.emit('selection.cleared');
    }

    getSelected () {
        return this._stack[0];
    }

    selectParent () {
        this._stack.shift();
        let newSelectedElement = this._stack[0];

        if (!newSelectedElement) {
            return;
        }

        this.emit('selection.selected', newSelectedElement);
    }

    hasParentToSelect () {
        return this._stack.length > 1;
    }

    clearIfRootAchived () {
        if (this._rootAchived) {
            this._stack = [];
            this._firstElementSelected = false;
        }
    }

    emit (event, ...args) {
        this._emitter(event, ...args);
    }
}

export default {
    props: ['structure', 'messenger'],
    components: { Section },
    data () {
        return {
            width: '100%',
            hoverable: {
                manager: new Hoverable(this.updateHoverableStyle),
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                }
            },
            selectable: {
                manager: new Selectable(this.updateSelectableStyle),
                selection: new Selection((...args) => {this.$root.$emit(...args)}),
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
            this.selectable.selection.selectParent();
        },
        deleteSelectedElement: function () {
            let element = this.selectable.selection.getSelected();


            if (element.type === 'block') {
                let index = element.parent.blocks.indexOf(element.object);

                if (index >= 0) {
                    element.parent.blocks.splice(index, 1);
                    this.selectable.selection.clearSelection();
                }
            } else if (element.type === 'column') {
                let index = element.parent.columns.indexOf(element.object);

                if (index >= 0) {
                    element.parent.columns.splice(index, 1);
                    this.selectable.selection.clearSelection();
                }
            } else if (element.type === 'row') {
                let index = element.parent.rows.indexOf(element.object);

                if (index >= 0) {
                    element.parent.rows.splice(index, 1);
                    this.selectable.selection.clearSelection();
                }
            } else if (element.type === 'section') {
                let index = this.structure.sections.indexOf(element.object);

                if (index >= 0) {
                    this.structure.sections.splice(index, 1);
                    this.selectable.selection.clearSelection();
                }
            }
        },
        updateElementActions: function (element, type, object) {
            this.actions.activeness.selectParent = this.selectable.selection.hasParentToSelect();
        }
    },
    mounted () {
        /**
         * Local listeners
         */
        this.$root.$on('structure.hoverable.enter', (el, type) => {
            this.hoverable.manager.enter(el);
        });
        this.$root.$on('structure.hoverable.leave', (el, type) => {
            this.hoverable.manager.leave(el);
        });
        this.$root.$on('block.inner.updated', () => {
            this.hoverable.manager.update();
        });
        this.$root.$on('structure.selectable.select', (el, type, object, parent) => {
            this.selectable.selection.selectElementUsingStackUntillRoot(type, {
                el: el,
                type: type,
                object: object,
                parent: parent,
            });
        });
        this.$root.$on('structure.selectable.hide', () => {
            this.selectable.manager.hide();
        });
        this.$root.$on('structure.selectable.show', () => {
            this.selectable.manager.show();
        });
        this.$root.$on('editor.save', () => {
            this.selectable.manager.hide();
            this.hoverable.manager.hide();
        });
        this.$root.$on('selection.selected', (element) => {
            this.selectable.manager.select(element.el);
            this.updateElementActions(element.el, element.type, element.object);
        });
        this.$root.$on('selection.cleared', () => {
            this.selectable.manager.deselect();
        });

        /**
         * Global listeners
         */
        this.messenger.listen('editor.cancel', () => {
            this.selectable.manager.hide();
            this.hoverable.manager.hide();
        });
        this.messenger.listen('structure.selectable.outsite', () => {
            this.selectable.manager.hide();
        });
        this.messenger.listen('device.size.changed', () => {
            let animationTime = 300;

            this.selectable.manager.keepUpdatePositionFor(animationTime);
        });
    }
};
</script>
