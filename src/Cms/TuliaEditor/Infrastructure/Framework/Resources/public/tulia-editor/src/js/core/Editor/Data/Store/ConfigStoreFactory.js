import { defineStore } from "pinia";
import { getSectionState } from "core/Shared/Structure/Element/Config/Defaults/SectionDefaults";
import { getColumnState } from "core/Shared/Structure/Element/Config/Defaults/ColumnDefaults";
import BlockDefaults from "core/Shared/Structure/Element/Config/Defaults/BlockDefaults";

export default class ConfigStoreFactory {
    constructor(blocksRegistry, structureStore) {
        this.blocksRegistry = blocksRegistry;
        this.structureStore = structureStore;
        this.blockDefaults = new BlockDefaults();
    }

    forSection(id, currents) {
        return defineStore(`config:section:${id}`, {
            state: () => getSectionState(currents),
            actions: {
                replace(config) {
                    for (let i in config) {
                        this[i] = config[i];
                    }
                }
            }
        });
    }

    forColumn(id, currents) {
        return defineStore(`config:column:${id}`, {
            state: () => getColumnState(currents),
            actions: {
                replace(config) {
                    for (let i in config) {
                        this[i] = config[i];
                    }
                }
            },
        });
    }

    forBlock(id, currents) {
        const block = this.structureStore.find(id);
        const definition = this.blocksRegistry.get(block.code);

        const actions = definition.store.config.actions || {};
        const getters = definition.store.config.getters || {};

        actions.replace = function(config) {
            for (let i in config) {
                this[i] = config[i];
            }
        };

        return defineStore(`config:block:${id}`, {
            state: () => this.blockDefaults.getBlockState(id, definition.store.config.state(), currents),
            getters: getters,
            actions: actions,
        });
    }
}
