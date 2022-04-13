export default class Selected {
    selectedElement;
    positionUpdateAnimationFrameHandle;
    viewUpdater;
    hidden = false;

    constructor (viewUpdater) {
        this.viewUpdater = viewUpdater;
    }

    highlightSelected (element) {
        this.selectedElement = {
            element: element
        };
        this.updatePosition();
    }

    clearSelectionHighlight () {
        this.selectedElement = null;
        this.resetPosition();
    }

    hide () {
        this.hidden = true;
        this.resetPosition();
    }

    show () {
        this.hidden = false;
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

    updatePosition () {
        if (!this.selectedElement || this.hidden) {
            return;
        }

        let doc = this.selectedElement.element.ownerDocument;

        this.viewUpdater({
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
        this.viewUpdater({
            top: -100,
            left: -100,
            width: 0,
            height: 0,
            tagName: null,
            scrollTop : 0
        });
    }
};
