import { defineStore } from 'pinia';
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const useStructureStore = defineStore('structure', {
    state: () => {
        return {
            sections: [],
            rows: [],
            columns: [],
            blocks: [],
        };
    },
    actions: {
        appendSection(section) {
            this.sections.push({
                id: section.id,
            });
        },
    },
    getters: {
        export() {
            return ObjectCloner.deepClone({
                sections: this.sections,
                rows: this.rows,
                columns: this.columns,
                blocks: this.blocks,
            });
        },
    },
});
