export default class SelectionSubscriber {
    constructor(selectedElementBoundaries, hoveredElementBoundaries) {
        this.selectedElementBoundaries = selectedElementBoundaries;
        this.hoveredElementBoundaries = hoveredElementBoundaries;
    }

    select(newSelected) {
        this.selectedElementBoundaries.highlightSelected(newSelected.id, newSelected.type);
    }

    deselect() {
        this.selectedElementBoundaries.clearSelectionHighlight();
    }

    hover(newHovered) {
        this.hoveredElementBoundaries.highlightHovered(newHovered.id, newHovered.type);
    }

    dehover() {
        this.hoveredElementBoundaries.clear();
    }

    update() {
        setTimeout(() => this.selectedElementBoundaries.updatePosition(), 50);
    }
}
