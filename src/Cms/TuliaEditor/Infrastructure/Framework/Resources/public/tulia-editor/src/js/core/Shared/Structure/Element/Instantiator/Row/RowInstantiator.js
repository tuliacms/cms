import AbstractInstantiator from "core/Shared/Structure/Element/Instantiator/AbstractInstantiator";
import Editor from "core/Shared/Structure/Element/Instantiator/Row/Editor";
import Manager from "core/Shared/Structure/Element/Instantiator/Row/Manager";
import Render from "core/Shared/Structure/Element/Instantiator/Row/Render";

export default class RowInstantiator extends AbstractInstantiator {
    constructor(messenger, elementConfigStoreRegistry) {
        super('row', messenger, elementConfigStoreRegistry);
    }

    createInstance(elementId, args, segment) {
        if (segment === 'manager') {
            return new Manager(...args);
        } else if (segment === 'editor') {
            return new Editor(...args);
        } else if (segment === 'render') {
            return new Render(...args);
        }
    }
}
