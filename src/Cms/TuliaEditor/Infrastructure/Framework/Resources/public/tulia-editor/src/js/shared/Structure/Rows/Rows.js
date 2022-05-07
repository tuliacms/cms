const AbstractElements = require('shared/Structure/Element/AbstractElements.js').default;

export default class Rows extends AbstractElements {
    constructor (options, messenger, extensions, childrenManager) {
        super('row', options, messenger, extensions, childrenManager);
    }
}
