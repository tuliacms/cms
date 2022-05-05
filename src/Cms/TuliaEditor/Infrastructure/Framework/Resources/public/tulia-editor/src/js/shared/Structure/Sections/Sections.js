const AbstractElements = require('shared/Structure/Element/AbstractElements.js').default;

export default class Sections extends AbstractElements {
    constructor (options, messenger, extensions) {
        super('section', options, messenger, extensions);
    }
}
