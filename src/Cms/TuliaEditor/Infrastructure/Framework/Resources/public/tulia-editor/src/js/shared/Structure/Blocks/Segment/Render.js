const _ = require('lodash');
const AbstractSegment = require('shared/Structure/Blocks/Segment/AbstractSegment.js').default;

export default class Render extends AbstractSegment {
    constructor (props, options, messenger, extensions) {
        super('render', props, options, messenger, extensions);
    }

    style (styles) {
        let id = _.uniqueId(`tued-element-style-`);

        this.styleSynchronizer.reactiveStyle[id] = styles;

        return id;
    }
}