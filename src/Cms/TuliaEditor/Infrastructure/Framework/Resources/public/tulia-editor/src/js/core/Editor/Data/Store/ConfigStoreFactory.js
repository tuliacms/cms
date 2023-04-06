import { defineStore } from "pinia";
import { getSectionState } from "core/Shared/Structure/Element/Config/Defaults/SectionDefaults";

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
}
