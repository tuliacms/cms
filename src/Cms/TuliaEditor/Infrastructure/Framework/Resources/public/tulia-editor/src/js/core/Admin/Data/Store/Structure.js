import { defineStore } from 'pinia';
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

const find = function (sections, id) {
    for (let i in sections) {
        if (sections[i].id === id) {
            return sections[i];
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

            /*let columns = rows[rk].columns;

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
            }*/
        }
    }
};

const removeFromCollection = function (collection, id) {
    let index = null;

    for (let i = 0; i <= collection.length; i++) {
        if (collection[i].id === id) {
            index = i;
            break;
        }
    }

    if (index !== null) {
        collection.splice(index, 1);
    }
};

export const useStructureStore = defineStore('structure', {
    state: () => {
        return {
            sections: [],
        };
    },
    actions: {
        moveUsingDelta(delta) {
            switch(delta.element.type) {
                case 'section':
                    let section = find(this.sections, delta.element.id);
                    this.removeSection(delta.element.id);
                    this.sections.splice(delta.to.index, 0, section);
                    break;
                case 'row':
                    let parent = findParent(this.sections, delta.element.id);
                    let row = find(parent.rows, delta.element.id);
                    this.removeRow(delta.element.id);
                    parent.rows.splice(delta.to.index, 0, row);
                    break;
            }
        },
        appendSection(section) {
            this.sections.push({
                id: section.id,
                // Type is used for draggable modifications, because this is universal mechanism
                type: 'section',
                rows: [],
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
            removeFromCollection(this.sections, id);
        },
        appendRow(row, sectionId) {
            find(this.sections, sectionId).rows.push({
                id: row.id,
                parent: sectionId,
                // Type is used for draggable modifications, because this is universal mechanism
                type: 'row',
            });
        },
        removeRow(id) {
            removeFromCollection(findParent(this.sections, id).rows, id);
        }
    },
    getters: {
        export(state) {
            return ObjectCloner.deepClone({
                sections: state.sections,
            });
        },
        rowsOf(state) {
            return (sectionId) => {
                return find(state.sections, sectionId).rows;
            };
        },
    },
});
