export default class Selected {
    selectedElement;
    positionUpdateAnimationFrameHandle;
    viewUpdater;
    hidden = false;

    constructor (viewUpdater) {
        this.viewUpdater = viewUpdater;
    }

    highlightSelected (element) {
        this.selectedElement = element;
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

        let doc = this.selectedElement.ownerDocument;

        this.viewUpdater({
            top: this.selectedElement.offsetTop,
            left: this.selectedElement.offsetLeft,
            width: this.selectedElement.offsetWidth,
            height: this.selectedElement.offsetHeight,
            tagName: this.selectedElement.dataset.tagname ?? this.selectedElement.tagName,
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
