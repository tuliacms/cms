import ElementOffset from "core/Editor/Selection/Boundaries/ElementOffset";

export default class SelectedElementBoundaries {
    selectedElement;
    positionUpdateAnimationFrameHandle;
    hidden = false;
    htmlNodeFinder;

    constructor(selection) {
        this.selection = selection;
    }

    registerHtmlNodeFinder(htmlNodeFinder) {
        this.htmlNodeFinder = htmlNodeFinder;
    }

    highlightSelected(id, type) {
        this.selectedElement = this.htmlNodeFinder(id, type);
        this.updatePosition();
    }

    clearSelectionHighlight() {
        this.selectedElement = null;
        this.resetPosition();
    }

    hide() {
        this.hidden = true;
        this.resetPosition();
    }

    show() {
        this.hidden = false;
        this.updatePosition();
    }

    update() {
        this.updatePosition();
    }

    keepUpdatePositionFor(microseconds) {
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

    updatePosition() {
        if (!this.selectedElement || this.hidden) {
            return;
        }

        let doc = this.selectedElement.ownerDocument;

        let offset = ElementOffset.get(this.selectedElement);

        this.selection.selected.boundaries.top = offset.top;
        this.selection.selected.boundaries.left = offset.left;
        this.selection.selected.boundaries.width = this.selectedElement.offsetWidth;
        this.selection.selected.boundaries.height = this.selectedElement.offsetHeight;
        this.selection.selected.tagName = this.selectedElement.dataset.tagname ?? this.selectedElement.tagName;
        // @todo Remove dependency to jQuery
        this.selection.selected.scrollTop = $(doc.defaultView || doc.parentWindow).scrollTop();
    }

    resetPosition() {
        this.selection.selected.boundaries.top = -100;
        this.selection.selected.boundaries.left = -100;
        this.selection.selected.boundaries.width = 0;
        this.selection.selected.boundaries.height = 0;
        this.selection.selected.tagName = null;
        // @todo Remove dependency to jQuery
        this.selection.selected.scrollTop = 0;
    }
};
