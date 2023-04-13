import AbstractInstantiator from "core/Shared/Structure/Element/Instantiator/AbstractInstantiator";
import Editor from "core/Shared/Structure/Element/Instantiator/Column/Editor";
import Manager from "core/Shared/Structure/Element/Instantiator/Column/Manager";
import Render from "core/Shared/Structure/Element/Instantiator/Column/Render";

export default class ColumnInstantiator extends AbstractInstantiator {
    constructor(elementConfigStoreRegistry) {
        super('column', elementConfigStoreRegistry);
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
