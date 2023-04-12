import { defineStore } from "pinia";
import { getSectionState } from "core/Shared/Structure/Element/Config/Defaults/SectionDefaults";
import { getColumnState } from "core/Shared/Structure/Element/Config/Defaults/ColumnDefaults";

export default class ConfigStoreFactory {
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
}
