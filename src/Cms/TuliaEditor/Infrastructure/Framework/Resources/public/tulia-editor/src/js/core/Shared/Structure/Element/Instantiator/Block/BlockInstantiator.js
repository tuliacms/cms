import AbstractInstantiator from "core/Shared/Structure/Element/Instantiator/AbstractInstantiator";
import BlockInstance from "core/Shared/Structure/Element/Instantiator/Block/BlockInstance";

export default class BlockInstantiator extends AbstractInstantiator {
    type = 'block';

    constructor(
        structureStore,
        blockRegistry,
    ) {
        super();
        this.blockRegistry = blockRegistry;
        this.structureStore = structureStore;
    }

    createInstance(elementId, args) {
        const instance = new BlockInstance(...args);

        const block = this.structureStore.find(elementId);
        instance.setDetails(this.blockRegistry.get(block.code));

        return instance;
    }
}
