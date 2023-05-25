import AbstractInstance from "core/Shared/Structure/Element/Instantiator/AbstractInstance";

export default class AbstractInstantiator {
    type = null;

    createInstance(elementId, args) {
        return new AbstractInstance(...args);
    }
}
