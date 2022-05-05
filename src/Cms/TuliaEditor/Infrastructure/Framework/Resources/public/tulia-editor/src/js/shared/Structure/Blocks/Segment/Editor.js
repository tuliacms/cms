const AbstractSegment = require('shared/Structure/Blocks/Segment/AbstractSegment.js').default;

export default class Editor extends AbstractSegment {
    constructor (props, options, messenger, extensions) {
        super('editor', props, options, messenger, extensions);
    }

    init () {
        this.dataSynchronizer.onChange(() => {
            this.messenger.notify('structure.element.updated', this.id);
        });
    }
}
