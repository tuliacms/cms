module.exports = class DraggableDeltaTranslator {
    delta = [];
    element = null;

    handle (delta) {
        let newElement = null;

        if (delta.moved) {
            newElement = delta.moved.element;
        }
        if (delta.added) {
            newElement = delta.added.element;
        }
        if (delta.removed) {
            newElement = delta.removed.element;
        }

        if (this.element === null) {
            this.element = newElement;
        } else {
            if (this.element !== newElement) {
                throw new Error('Cannot handle deltas for different elements in one instance of DraggableDeltaTranslator.');
            }
        }
    }

    stop (event) {
        if (!this.element) {
            return;
        }

        return {
            from: {
                parent: this.translateParent(event.from.__vue__.$el.dataset.draggableDeltaTransformerParent),
                index: event.oldIndex,
            },
            to: {
                parent: this.translateParent(event.to.__vue__.$el.dataset.draggableDeltaTransformerParent),
                index: event.newIndex,
            },
            element: {
                id: this.element.id,
                type: this.element.type,
            }
        }
    }

    translateParent (parent) {
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
};
