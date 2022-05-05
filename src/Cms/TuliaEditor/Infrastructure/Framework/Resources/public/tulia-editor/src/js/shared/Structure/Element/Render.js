const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;
const _ = require('lodash');

export default class Render extends AbstractSegment {
    constructor (type, element, options, messenger, extensions) {
        super('render', type, element, options, messenger, extensions);
    }

    style (styles) {
        let id = _.uniqueId(`tued-element-style-`);

        this.styleSynchronizer.reactiveStyle[id] = styles;

        return id;
    }
}
