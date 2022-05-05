const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;

export default class Editor extends AbstractSegment {
    constructor (type, element, options, messenger, extensions) {
        super('editor', type, element, options, messenger, extensions);
    }

    init () {
        this.dataSynchronizer.onChange(() => {
            this.messenger.notify('structure.element.updated', this.id);
        });
    }
}
