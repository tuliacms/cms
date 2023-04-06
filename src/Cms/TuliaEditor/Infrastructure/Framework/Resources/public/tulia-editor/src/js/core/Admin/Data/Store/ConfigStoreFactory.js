import { defineStore } from "pinia";
import { getSectionState, exportSectionState } from "core/Shared/Structure/Element/Config/Defaults/SectionDefaults";

export default class ConfigStoreFactory {
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
}
