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
                left: actions.style.left + 'px',
                top: actions.style.top + 'px',
            }"
        >
            <div class="tued-element-action" title="Zaznacz blok wyżej"><i class="fas fa-long-arrow-alt-up"></i></div>
            <div class="tued-element-action" title="Duplikuj"><i class="fas fa-copy"></i></div>
            <div class="tued-element-action" title="Usuń"><i class="fas fa-trash"></i></div>
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

    /**
     * Blocking is a feature that allows us to not stopping propagation on DOM,
     * but select only first clickem element in DOM. Usage:
     * 1. Bind `mousedown` on all selectable elements
     * 2. Bind `mousedown` od a Root element (this is a root of all selectable elements, there propagation stopped)
     * 3. On any selectable element call first select(element) and then call blockSelected().
     * 4. On root element call unblockSelected()
     * In this case, we select only first call of the select(element), omit the selections (elements) between
     * first one, and stop at the root.
     *
     * @type {boolean}
     */
    blocked = false;

    constructor (positionUpdater) {
        this.positionUpdater = positionUpdater;
    }

    select (element) {
        if (this.blocked) {
            return;
        }

        this.selectedElement = element;
        this.updatePosition();
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

    unblockSelected () {
        this.blocked = false;
    }

    blockSelected () {
        this.blocked = true;
    }

    positionUpdateAnimationFrameHandle;

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
                    top: -100
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
            this.actions.style.left = style.width + style.left - elm.offsetWidth;
        },
        updateElementActions: function (element, type) {
            console.log(element, type);
        }
    },
    mounted() {
        this.$root.$on('structure.hoverable.enter', (el, type) => {
            this.hoverable.manager.enter(el);
        });
        this.$root.$on('structure.hoverable.leave', (el, type) => {
            this.hoverable.manager.leave(el);
        });
        this.$root.$on('block.inner.updated', () => {
            this.hoverable.manager.update();
        });
        this.$root.$on('structure.selectable.select', (el, type) => {
            if (type === 'root') {
                this.selectable.manager.unblockSelected();
            } else {
                this.selectable.manager.select(el);
                this.selectable.manager.blockSelected();
            }

            this.updateElementActions(el, type);
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
