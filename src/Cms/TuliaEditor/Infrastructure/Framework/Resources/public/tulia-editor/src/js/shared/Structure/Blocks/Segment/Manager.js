const AbstractSegment = require('shared/Structure/Blocks/Segment/AbstractSegment.js').default;

export default class Manager extends AbstractSegment {
    constructor (props, options, messenger, extensions) {
        super('manager', props, options, messenger, extensions);
    }

    init () {
        this.messenger.on('structure.element.created', (type, id) => {
            if (type === 'block' && id === this.id) {
                this.notify('created');
            }
        });
    }
}
