import { defineStore } from "pinia";
import BlockDefaults from "core/Shared/Structure/Element/Data/Defaults/BlockDefaults";
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class DataStoreFactory {
    constructor(blocksRegistry, structureStore) {
        this.blocksRegistry = blocksRegistry;
        this.structureStore = structureStore;
        this.blockDefaults = new BlockDefaults();
    }

    forBlock(id, currents) {
        const block = this.structureStore.find(id);
        const definition = this.blocksRegistry.get(block.code);

        const actions = definition.store?.data?.actions || {};
        const getters = definition.store?.data?.getters || {};

        actions.replace = function(config) {
            config = ObjectCloner.deepClone(config);

            for (let i in config) {
                this[i] = config[i];
            }
        };
        getters.export = (state) => this.blockDefaults.exportBlockState(id, state);

        return defineStore(`data:block:${id}`, {
            state: () => this.blockDefaults.getBlockState(id, definition.store?.data?.state() ?? {}, currents),
            getters: getters,
            actions: actions,
        });
    }
}
