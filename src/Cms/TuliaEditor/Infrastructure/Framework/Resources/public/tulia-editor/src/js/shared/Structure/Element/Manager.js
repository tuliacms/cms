const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;

export default class Manager extends AbstractSegment {
    constructor (type, element, options, messenger, extensions, childrenManager) {
        super('manager', type, element, options, messenger, extensions, childrenManager);
    }

    init () {
        this.messenger.on('structure.element.created', (type, id) => {
            if (type === this.type && id === this.id) {
                this.notify('created');
            }
        });
    }

    getChildren (sourceChildren) {
        let children = [];

        for (let i in sourceChildren) {
            children.push(this.childrenManager.manager(sourceChildren[i]));
        }

        return children;
    }
}
