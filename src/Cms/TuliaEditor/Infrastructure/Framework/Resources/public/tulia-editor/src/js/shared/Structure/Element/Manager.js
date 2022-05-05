const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;

export default class Manager extends AbstractSegment {
    constructor (type, element, options, messenger, extensions) {
        super('manager', type, element, options, messenger, extensions);
    }

    init () {
        this.messenger.on('structure.element.created', (type, id) => {
            if (type === this.type && id === this.id) {
                this.notify('created');
            }
        });
    }
}
