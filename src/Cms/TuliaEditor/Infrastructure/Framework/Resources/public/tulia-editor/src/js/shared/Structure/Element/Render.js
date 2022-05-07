const AbstractSegment = require('shared/Structure/Element/AbstractSegment.js').default;
const _ = require('lodash');

export default class Render extends AbstractSegment {
    constructor (type, element, options, messenger, extensions, childrenManager) {
        super('render', type, element, options, messenger, extensions, childrenManager);
    }

    style (styles) {
        let id = _.uniqueId(`tued-element-style-`);

        this.styleSynchronizer.reactiveStyle[id] = styles;

        return id;
    }

    getChildren (sourceChildren) {
        let children = [];

        for (let i in sourceChildren) {
            children.push(this.childrenManager.render(sourceChildren[i]));
        }

        return children;
    }
}
