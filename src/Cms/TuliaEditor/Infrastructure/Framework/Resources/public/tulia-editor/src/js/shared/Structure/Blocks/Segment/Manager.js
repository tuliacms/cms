const _ = require('lodash');
const AbstractSegment = require('shared/Structure/Blocks/Segment/AbstractSegment.js').default;

export default class Manager extends AbstractSegment {
    constructor (props, options, messenger) {
        super('manager', props, options, messenger);
    }
}
