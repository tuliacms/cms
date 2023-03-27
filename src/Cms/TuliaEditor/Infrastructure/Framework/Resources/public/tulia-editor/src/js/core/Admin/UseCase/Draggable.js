export default class Draggable {
    constructor(selection, eventBus) {
        this.selection = selection;
        this.eventBus = eventBus;
    }

    start(id, type) {
        this.selection.disableSelection();
        this.selection.disableHovering();
        this.eventBus.dispatch('draggable.start', { id, type });
    }

    end(id, type) {
        this.selection.enableSelection();
        this.selection.enableHovering();
        this.selection.select(id, type);
        this.selection.hover(id, type);
        this.eventBus.dispatch('draggable.end', { id, type });
    }
}
