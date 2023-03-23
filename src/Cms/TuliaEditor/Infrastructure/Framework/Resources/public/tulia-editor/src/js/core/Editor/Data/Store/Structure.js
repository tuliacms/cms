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
        update(structure) {
            this.sections = structure.sections;
            this.rows = structure.rows;
            this.columns = structure.columns;
            this.blocks = structure.blocks;
        },
    },
});
