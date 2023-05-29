class DraggableDeltaTranslator {
    delta = [];
    element = null;

    constructor(delta) {
        if (delta.moved) {
            this.element = delta.moved.element;
        }
        if (delta.added) {
            this.element = delta.added.element;
        }
        if (delta.removed) {
            this.element = delta.removed.element;
        }
    }

    stop(event) {
        if (!this.element) {
            return;
        }

        return {
            from: {
                parent: DraggableDeltaTranslator.translateParent(event.from.dataset.draggableDeltaTransformerParent),
                index: event.oldIndex,
            },
            to: {
                parent: DraggableDeltaTranslator.translateParent(event.to.dataset.draggableDeltaTransformerParent),
                index: event.newIndex,
            },
            element: {
                id: this.element.id,
                type: this.element.type,
            }
        }
    }

    static translateParent(parent) {
        if (!parent) {
            return {
                type: 'structure'
            }
        }

        let [type, id] = parent.split('.');

        return {
            id: id,
            type: type,
        }
    }
}


export default class Draggable {
    constructor(selection, structure, eventBus, messenger) {
        this.selection = selection;
        this.structure = structure;
        this.eventBus = eventBus;
        this.messenger = messenger;
    }

    start(event) {
        this.selection.disableSelection();
        this.selection.disableHovering();
        this.eventBus.dispatch('draggable.start', { id: event.item.dataset.elementId, type: event.item.dataset.elementType });
    }

    change(event) {
        this.deltaTranslator = new DraggableDeltaTranslator(event);
    }

    end(event) {
        this.structure.moveUsingDelta(this.deltaTranslator.stop(event));

        this.selection.enableSelection();
        this.selection.enableHovering();
        this.selection.select(event.item.dataset.elementId, event.item.dataset.elementType);
        this.selection.hover(event.item.dataset.elementId, event.item.dataset.elementType);
        this.eventBus.dispatch('draggable.end', { id: event.item.dataset.elementId, type: event.item.dataset.elementType });

        this.update();
    }

    update() {
        this.messenger.send('admin.structure.changed', {
            structure: this.structure.export,
        });
    }
}
