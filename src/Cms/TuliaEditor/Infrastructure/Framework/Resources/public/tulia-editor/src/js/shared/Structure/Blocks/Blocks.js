const AbstractElements = require('shared/Structure/Element/AbstractElements.js').default;

export default class Blocks extends AbstractElements {
    constructor (options, messenger, extensions) {
        super('block', options, messenger, extensions);
    }
}
