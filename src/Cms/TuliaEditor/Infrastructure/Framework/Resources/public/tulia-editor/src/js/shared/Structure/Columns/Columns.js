const AbstractElements = require('shared/Structure/Element/AbstractElements.js').default;

export default class Columns extends AbstractElements {
    constructor (options, messenger, extensions, childrenManager) {
        super('column', options, messenger, extensions, childrenManager);
    }
}
