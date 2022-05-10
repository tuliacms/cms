const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;

export default class Editor extends AbstractSegment {
    init () {
        this.dataSynchronizer.onChange(() => {
            this.messenger.notify('structure.element.updated', this.id);
        });
    }

    getSegment () {
        return 'editor';
    }
}
