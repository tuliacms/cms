import { defineStore } from 'pinia';

const find = function (sections, id) {
    for (let sk in sections) {
        if (sections[sk].id === id) {
            return sections[sk];
        }

        let rows = sections[sk].rows;

        for (let rk in rows) {
            if (rows[rk].id === id) {
                return rows[rk];
            }

            let columns = rows[rk].columns;

            for (let ck in columns) {
                if (columns[ck].id === id) {
                    return columns[ck];
                }

                let blocks = columns[ck].blocks;

                for (let bk in blocks) {
                    if (blocks[bk].id === id) {
                        return blocks[bk];
                    }
                }
            }
        }
    }
};

const findParent = function (sections, childId) {
    for (let sk in sections) {
        let parentSection = sections[sk];

        let rows = sections[sk].rows;

        for (let rk in rows) {
            if (rows[rk].id === childId) {
                return parentSection;
            }

            let parentRow = rows[rk];

            let columns = rows[rk].columns;

            for (let ck in columns) {
                if (columns[ck].id === childId) {
                    return parentRow;
                }

                let parentColumn = columns[ck];

                let blocks = columns[ck].blocks;

                for (let bk in blocks) {
                    if (blocks[bk].id === childId) {
                        return parentColumn;
                    }
                }
            }
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
            this.sections = structure?.sections ?? [];
        },
    },
    getters: {
        find(state) {
            return (id) => {
                return find(state.sections, id);
            };
        },
        findParent(state) {
            return (childId) => {
                return findParent(state.sections, childId);
            };
        },
        rowsOf(state) {
            return (sectionId) => {
                return find(state.sections, sectionId).rows;
            };
        },
        columnsOf(state) {
            return (rowId) => {
                return find(state.sections, rowId).columns;
            };
        },
        blocksOf(state) {
            return (columnId) => {
                return find(state.sections, columnId).blocks;
            };
        },
    },
});
