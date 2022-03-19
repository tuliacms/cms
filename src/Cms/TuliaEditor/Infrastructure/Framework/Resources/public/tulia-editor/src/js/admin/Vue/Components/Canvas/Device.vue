<template>
    <div
        class="tued-canvas-device-faker"
        :style="{ width: this.width }"
    >
        <div class="tued-element-boundaries tued-element-selected-boundaries tued-hidden">
            <div class="tued-node-name"></div>
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
        <Structure :structure="structure"></Structure>
    </div>
</template>

<script>
import Structure from  '../Structure/Structure.vue';

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
        this.updatePositionToElement();
    }

    leave () {
        this.hoveredStack.pop();

        if (this.hoveredStack[this.hoveredStack.length - 1]) {
            this.hoveredElement = this.hoveredStack[this.hoveredStack.length - 1];
            this.updatePositionToElement();
        } else {
            this.resetPosition();
        }
    }

    update () {
        this.updatePositionToElement();
    }

    updatePositionToElement () {
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

export default {
    props: ['structure'],
    components: {
        Structure
    },
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
            }
        }
    },
    methods: {
        changeSizeTo: function (size) {
            this.width = size.width + 'px';
        },
        updateHoverableStyle: function (style) {
            this.hoverable.style.left = style.left;
            this.hoverable.style.top = style.top;
            this.hoverable.style.width = style.width;
            this.hoverable.style.height = style.height;
        }
    },
    mounted() {
        this.$root.$on('canvas.size.changed', (size) => {
            this.changeSizeTo(size);
        });

        this.$root.$on('structure.hoverable.enter', (el, type) => {
            this.hoverable.manager.enter(el);
        });
        this.$root.$on('structure.hoverable.leave', (el, type) => {
            this.hoverable.manager.leave(el);
        });
        this.$root.$on('block.inner.updated', () => {
            this.hoverable.manager.update();
        });
    }
};
</script>
