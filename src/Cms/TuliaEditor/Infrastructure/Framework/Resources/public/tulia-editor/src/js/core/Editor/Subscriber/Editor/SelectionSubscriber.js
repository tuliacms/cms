export default class SelectionSubscriber {
    constructor(selectedElementBoundaries, hoveredElementBoundaries) {
        this.selectedElementBoundaries = selectedElementBoundaries;
        this.hoveredElementBoundaries = hoveredElementBoundaries;
    }

    static getSubscribedEvents() {
        return {
            'editor.ready': 'registerUpdater',
            'selection.selected': 'select',
            'selection.deselected': 'deselect',
            'selection.hovered': 'hover',
            'selection.dehovered': 'dehover',
            'structure.changed': 'deselect',
        };
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
            () => {
                this.selectedElementBoundaries.update();
                this.hoveredElementBoundaries.update();
            },
            120
        );
    }
}
