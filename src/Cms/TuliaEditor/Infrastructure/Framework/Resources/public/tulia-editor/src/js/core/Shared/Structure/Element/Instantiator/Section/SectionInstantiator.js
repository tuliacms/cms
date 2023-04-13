import AbstractInstantiator from "core/Shared/Structure/Element/Instantiator/AbstractInstantiator";
import Editor from "core/Shared/Structure/Element/Instantiator/Section/Editor";
import Manager from "core/Shared/Structure/Element/Instantiator/Section/Manager";
import Render from "core/Shared/Structure/Element/Instantiator/Section/Render";

export default class SectionInstantiator extends AbstractInstantiator {
    constructor(elementConfigStoreRegistry) {
        super('section', elementConfigStoreRegistry);
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
