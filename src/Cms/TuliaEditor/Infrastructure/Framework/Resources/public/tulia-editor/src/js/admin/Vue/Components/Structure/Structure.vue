<template>
    <div
        class="tued-structure"
        @mousedown="$root.$emit('structure.selectable.select', $el, 'root')"
    >
        <Section
            v-for="section in structure.sections"
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

    update () {
        this.updatePosition();
    }

    updatePosition () {
        this.positionUpdater({
            width: this.hoveredElement.offsetWidth,
            height: this.hoveredElement.offsetHeight,
            top: this.hoveredElement.offsetTop,
            left: this.hoveredElement.offsetLeft,
        });
    }

    resetPosition () {
        this.positionUpdater({
            width: 0,
            height: 0,
            top: -100,
            left: -100,
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

        this.positionUpdater({
            width: this.selectedElement.offsetWidth,
            height: this.selectedElement.offsetHeight,
            top: this.selectedElement.offsetTop,
            left: this.selectedElement.offsetLeft,
            tagName: this.selectedElement.tagName
        });
    }

    resetPosition () {
        this.positionUpdater({
            width: 0,
            height: 0,
            top: -100,
            left: -100,
        });
    }
}


export default {
    props: ['structure'],
    components: { Section },
    data () {
        return {
            width: '100%',
            hoverable: {
                manager: new Hoverable(this.updateHoverableStyle),
                style: {
                    left: 0,
                    top: 0,
                    width: 0,
                    height: 0,
                }
            },
            selectable: {
                manager: new Selectable(this.updateSelectableStyle),
                style: {
                    left: 0,
                    top: 0,
                    width: 0,
                    height: 0,
                    tagName: 'div',
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
        });
        this.$root.$on('structure.selectable.hide', () => {
            this.selectable.manager.hide();
        });
        this.$root.$on('structure.selectable.show', () => {
            this.selectable.manager.show();
        });
        this.$root.$on('structure.selectable.outsite', () => {
            this.selectable.manager.hide();
        });
        this.$root.$on('device.size.changed', () => {
            let deviceFaker = this.$el.closest('.tued-canvas-device-faker');
            let transitionDuration = window.getComputedStyle(deviceFaker).transitionDuration;

            if (transitionDuration && transitionDuration.indexOf('s')) {
                transitionDuration = parseFloat(transitionDuration.replace('s', '')) * 1000;
                // Add 50ms for any browser rendering lag
                transitionDuration = transitionDuration + 50;
            } else {
                transitionDuration = 500;
            }

            this.selectable.manager.keepUpdatePositionFor(transitionDuration);
        });
    }
};
</script>
