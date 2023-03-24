export default class SelectionSubscriber {
    constructor(elementBoundaries) {
        this.elementBoundaries = elementBoundaries;
    }

    select(newSelected) {
        this.elementBoundaries.highlightSelected(newSelected.id, newSelected.type);
    }

    deselect() {
        this.elementBoundaries.clearSelectionHighlight();
    }

    update() {
        setTimeout(() => this.elementBoundaries.updatePosition(), 50);
    }
}
