import { defineStore } from 'pinia';

const find = function (collection, id) {
    for (let i in collection) {
        if (collection[i].id === id) {
            return collection[i];
        }
    }
};

export const useStructureStore = defineStore('structure', {
    state: () => {
        return {
            sections: [],
        };
    },
    actions: {
        update(structure) {
            this.sections = structure.sections;
        },
    },
    getters: {
        rowsOf(state) {
            return (sectionId) => {
                return find(state.sections, sectionId).rows;
            };
        },
    },
});
