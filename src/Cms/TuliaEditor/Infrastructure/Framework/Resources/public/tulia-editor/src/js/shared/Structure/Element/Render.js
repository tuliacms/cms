const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;
const _ = require('lodash');

export default class Render extends AbstractSegment {
    style (styles) {
        let id = _.uniqueId(`tued-element-style-`);

        this.styleSynchronizer.reactiveStyle[id] = styles;

        return id;
    }

    getSegment () {
        return 'render';
    }

    require (asset) {
        console.log(this);
        this.assets.require(asset);
    }
}
