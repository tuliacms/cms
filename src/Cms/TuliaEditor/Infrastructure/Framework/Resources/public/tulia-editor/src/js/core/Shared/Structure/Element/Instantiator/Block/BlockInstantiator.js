import AbstractInstantiator from "core/Shared/Structure/Element/Instantiator/AbstractInstantiator";
import Editor from "core/Shared/Structure/Element/Instantiator/Block/Editor";
import Manager from "core/Shared/Structure/Element/Instantiator/Block/Manager";
import Render from "core/Shared/Structure/Element/Instantiator/Block/Render";

export default class BlockInstantiator extends AbstractInstantiator {
    constructor(elementConfigStoreRegistry, elementDataStoreFactory, blockRegistry, structureStore) {
        super('block', elementConfigStoreRegistry, elementDataStoreFactory);

        this.blockRegistry = blockRegistry;
        this.structureStore = structureStore;
    }

    createInstance(elementId, args, segment) {
        let instance;

        if (segment === 'manager') {
            instance = new Manager(...args);
        } else if (segment === 'editor') {
            instance = new Editor(...args);
        } else if (segment === 'render') {
            instance = new Render(...args);
        }

        const block = this.structureStore.find(elementId);
        instance.setDetails(this.blockRegistry.get(block.code));

        return instance;
    }
}
