const _ = require('lodash');
const AbstractSegment = require('shared/Structure/Blocks/Segment/AbstractSegment.js').default;

export default class Manager extends AbstractSegment {
    constructor (props, options, messenger, extensions) {
        super('manager', props, options, messenger, extensions);
    }
}
