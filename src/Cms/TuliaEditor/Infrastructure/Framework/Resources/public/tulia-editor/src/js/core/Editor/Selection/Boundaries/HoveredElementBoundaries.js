import ElementOffset from "core/Editor/Selection/Boundaries/ElementOffset";

export default class HoveredElementBoundaries {
    hoveredElement;
    htmlNodeFinder;

    constructor(selection) {
        this.selection = selection;
    }

    registerHtmlNodeFinder(htmlNodeFinder) {
        this.htmlNodeFinder = htmlNodeFinder;
    }

    highlightHovered (id, type) {
        this.hoveredElement = this.htmlNodeFinder(id, type);
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

    updatePosition () {
        if (!this.hoveredElement) {
            return;
        }

        let offset = ElementOffset.get(this.hoveredElement);

        this.selection.hovered.boundaries.top = offset.top;
        this.selection.hovered.boundaries.left = offset.left;
        this.selection.hovered.boundaries.width = this.hoveredElement.offsetWidth;
        this.selection.hovered.boundaries.height = this.hoveredElement.offsetHeight;
    }

    resetPosition () {
        this.selection.hovered.boundaries.top = -100;
        this.selection.hovered.boundaries.left = -100;
        this.selection.hovered.boundaries.width = 0;
        this.selection.hovered.boundaries.height = 0;
    }
}
