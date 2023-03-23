import { defineStore } from 'pinia';

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
        }
    }
});
