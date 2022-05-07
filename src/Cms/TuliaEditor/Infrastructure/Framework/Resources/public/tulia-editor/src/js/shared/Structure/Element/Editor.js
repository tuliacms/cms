const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;

export default class Editor extends AbstractSegment {
    constructor (type, element, options, messenger, extensions, childrenManager) {
        super('editor', type, element, options, messenger, extensions, childrenManager);
    }

    init () {
        this.dataSynchronizer.onChange(() => {
            this.messenger.notify('structure.element.updated', this.id);
        });
    }

    getChildren (sourceChildren) {
        let children = [];

        for (let i in sourceChildren) {
            children.push(this.childrenManager.editor(sourceChildren[i]));
        }

        return children;
    }
}
