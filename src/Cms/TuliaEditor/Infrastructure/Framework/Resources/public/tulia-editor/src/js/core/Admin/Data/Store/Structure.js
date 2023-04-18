import { defineStore } from 'pinia';
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

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

const findElementsPosition = function (collection, id) {
    for (let i in collection) {
        if (collection[i].id === id) {
            return parseInt(i);
        }
    }

    return undefined;
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

const produceColumn = function (id, rowId) {
    return {
        id: id,
        parent: rowId,
        // Type is used for draggable modifications, because this is universal mechanism
        type: 'column',
        blocks: [],
    };
};

const produceBlock = function (id, code, columnId) {
    return {
        id: id,
        parent: columnId,
        // Type is used for draggable modifications, because this is universal mechanism
        type: 'block',
        code: code,
    };
};

export const useStructureStore = defineStore('structure', {
    state: () => {
        return {
            sections: [],
        };
    },
    actions: {
        replace(sections, rows, columns, blocks) {
            this.sections = [];

            for (let s in sections) {
                this.appendSection(sections[s]);
            }
            for (let s in rows) {
                this.appendRow(rows[s], rows[s].parent);
            }
            for (let s in columns) {
                this.appendColumn(columns[s], columns[s].parent);
            }
            for (let s in blocks) {
                this.appendBlock(blocks[s], blocks[s].parent);
            }
        },
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

            this.sections.splice(index + 1, 0, {
                id: section.id,
                // Type is used for draggable modifications, because this is universal mechanism
                type: 'section',
                rows: [],
            });
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
                columns: [],
            });
        },
        removeRow(id) {
            removeFromCollection(findParent(this.sections, id).rows, id);
        },
        appendColumn(column, rowId) {
            find(this.sections, rowId).columns.push(produceColumn(column.id, rowId));
        },
        appendColumnBefore(column, before) {
            const row = findParent(this.sections, before);
            const pos = findElementsPosition(row.columns, before);

            if (pos === undefined) {
                row.columns.push(produceColumn(column.id, row.id));
                return;
            }

            row.columns.splice(pos, 0, produceColumn(column.id, row.id));
        },
        appendColumnAfter(column, after) {
            const row = findParent(this.sections, after);
            const pos = findElementsPosition(row.columns, after);

            if (pos === undefined) {
                row.columns.push(produceColumn(column.id, row.id));
                return;
            }

            row.columns.splice(pos + 1, 0, produceColumn(column.id, row.id));
        },
        removeColumn(id) {
            removeFromCollection(findParent(this.sections, id).columns, id);
        },
        appendBlock(block, columnId) {
            find(this.sections, columnId).blocks.push(produceBlock(block.id, block.code, columnId));
        },
    },
    getters: {
        export(state) {
            return ObjectCloner.deepClone({
                sections: state.sections,
            });
        },
        find(state) {
            return (id) => {
                return find(state.sections, id);
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
