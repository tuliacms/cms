import { defineStore } from "pinia";
import { getSectionState, exportSectionState } from "core/Shared/Structure/Element/Config/Defaults/SectionDefaults";
import { getColumnState, exportColumnState } from "core/Shared/Structure/Element/Config/Defaults/ColumnDefaults";

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
}
