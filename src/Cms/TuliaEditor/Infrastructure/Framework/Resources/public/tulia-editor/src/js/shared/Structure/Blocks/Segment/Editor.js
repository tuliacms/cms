const _ = require('lodash');
const AbstractSegment = require('shared/Structure/Blocks/Segment/AbstractSegment.js').default;

export default class Editor extends AbstractSegment {
    constructor (props, options, messenger) {
        super('editor', props, options, messenger);
    }
}
