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
        appendSectionAfter(section, id) {
            let index = 0;

            for (let i = 0; i <= this.sections.length; i++) {
                if (this.sections[i].id === id) {
                    index = i;
                    break;
                }
            }

            this.sections.splice(index + 1, 0, { id: section.id });
        },
        removeSection(id) {
            let index = null;

            for (let i = 0; i <= this.sections.length; i++) {
                if (this.sections[i].id === id) {
                    index = i;
                    break;
                }
            }

            if (index !== null) {
                this.sections.splice(index, 1);
            }
        }
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
