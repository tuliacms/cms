import { defineStore } from "pinia";
import { getSectionState, exportSectionState } from "core/Shared/Structure/Element/Config/Defaults/SectionDefaults";
import { getColumnState, exportColumnState } from "core/Shared/Structure/Element/Config/Defaults/ColumnDefaults";
import BlockDefaults from "core/Shared/Structure/Element/Config/Defaults/BlockDefaults";
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class ConfigStoreFactory {
    constructor(blocksRegistry, structureStore) {
        this.blocksRegistry = blocksRegistry;
        this.structureStore = structureStore;
        this.blockDefaults = new BlockDefaults();
    }

    forSection(id, currents) {
        return defineStore(`config:section:${id}`, {
            state: () => getSectionState(currents),
            getters: {
                export(state) {
                    return exportSectionState(state);
                },
            },
        });
    }

    forColumn(id, currents) {
        return defineStore(`config:column:${id}`, {
            state: () => getColumnState(currents),
            getters: {
                export(state) {
                    return exportColumnState(state);
                },
            },
        });
    }

    forBlock(id, currents) {
        const block = this.structureStore.find(id);
        const definition = this.blocksRegistry.get(block.code);

        const actions = definition.store.config.actions || {};
        const getters = definition.store.config.getters || {};

        actions.replace = function(config) {
            config = ObjectCloner.deepClone(config);

            for (let i in config) {
                this[i] = config[i];
            }
        };
        getters.export = (state) => this.blockDefaults.exportBlockState(id, state);

        return defineStore(`config:block:${id}`, {
            state: () => this.blockDefaults.getBlockState(id, definition.store.config.state(), currents),
            getters: getters,
            actions: actions,
        });
    }
}
