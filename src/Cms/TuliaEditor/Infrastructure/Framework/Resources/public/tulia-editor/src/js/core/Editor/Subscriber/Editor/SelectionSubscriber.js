export default class SelectionSubscriber {
    constructor(selectedElementBoundaries, hoveredElementBoundaries) {
        this.selectedElementBoundaries = selectedElementBoundaries;
        this.hoveredElementBoundaries = hoveredElementBoundaries;
    }

    select(newSelected) {
        setTimeout(
            () => this.selectedElementBoundaries.highlightSelected(newSelected.id, newSelected.type),
            40
        );
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

    registerUpdater() {
        this.interval = setInterval(
            () => this.selectedElementBoundaries.update(),
            120
        );
    }
}
