export default class Hovered {
    hoveredElement;
    viewUpdater;

    constructor (viewUpdater) {
        this.viewUpdater = viewUpdater;
    }

    highlight (element, type, id) {
        this.hoveredElement = {
            element: element,
            type: type,
            id: id,
        };
        this.updatePosition();
    }

    clear () {
        this.hoveredElement = null;
        this.resetPosition();
    }

    hide () {
        this.hoveredElement = null;
        this.resetPosition();
    }

    update () {
        this.updatePosition();
    }

    /*disable () {
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
    }*/

    updatePosition () {
        /*if (this.isDisabled()) {
            return;
        }*/

        if (!this.hoveredElement) {
            return;
        }

        this.viewUpdater({
            top: this.hoveredElement.element.offsetTop,
            left: this.hoveredElement.element.offsetLeft,
            width: this.hoveredElement.element.offsetWidth,
            height: this.hoveredElement.element.offsetHeight,
        });
    }

    resetPosition () {
        this.viewUpdater({
            top: -100,
            left: -100,
            width: 0,
            height: 0,
        });
    }
}
