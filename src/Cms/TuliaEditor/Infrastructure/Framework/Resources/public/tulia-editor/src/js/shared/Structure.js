const { v4 } = require('uuid');

module.exports = class Structure {
    static find (structure, id) {
        for (let sk in structure.sections) {
            if (structure.sections[sk].id === id) {
                return structure.sections[sk];
            }

            let rows = structure.sections[sk].rows;

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

        return null;
    }

    static findParent (structure, childId) {
        let parent = null;

        for (let sk in structure.sections) {
            parent = structure.sections[sk];

            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                if (rows[rk].id === childId) {
                    return parent;
                }

                parent = rows[rk];

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (columns[ck].id === childId) {
                        return parent;
                    }

                    parent = columns[ck];

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (blocks[bk].id === childId) {
                            return parent;
                        }
                    }
                }
            }
        }

        return null;
    }

    static removeElement (structure, id) {
        for (let sk in structure.sections) {
            if (structure.sections[sk].id === id) {
                structure.sections.splice(sk, 1);
                return;
            }

            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                if (structure.sections[sk].rows[rk].id === id) {
                    structure.sections[sk].rows.splice(rk, 1);
                    return;
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (structure.sections[sk].rows[rk].columns[ck].id === id) {
                        structure.sections[sk].rows[rk].columns.splice(ck, 1);
                        return;
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (structure.sections[sk].rows[rk].columns[ck].blocks[bk].id === id) {
                            structure.sections[sk].rows[rk].columns[ck].blocks.splice(bk, 1);
                            return;
                        }
                    }
                }
            }
        }
    }

    static moveElementUsingDelta (structure, delta) {
        let element = this.find(structure, delta.element.id);

        if (delta.from.parent.type === 'structure' && delta.to.parent.type === 'structure') {
            this.removeElement(structure, delta.element.id);
            structure.sections.splice(delta.to.index, 0, element);
            return;
        }

        let newParent = this.find(structure, delta.to.parent.id);

        this.removeElement(structure, element.id);

        if (newParent.type === 'column') {
            newParent.blocks.splice(delta.to.index, 0, element);
        } else if (newParent.type === 'row') {
            newParent.columns.splice(delta.to.index, 0, element);
        } else if (newParent.type === 'section') {
            newParent.rows.splice(delta.to.index, 0, element);
        }
    }

    static ensureAllIdsAreUnique (structure) {
        let usedIds = [];

        for (let sk in structure.sections) {
            if (!structure.sections[sk].id || usedIds.indexOf(structure.sections[sk].id) >= 0) {
                structure.sections[sk].id = v4();
            }

            usedIds.push(structure.sections[sk].id);

            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                if (!rows[rk].id || usedIds.indexOf(rows[rk].id) >= 0) {
                    rows[rk].id = v4();
                }

                usedIds.push(rows[rk].id);

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (!columns[ck].id || usedIds.indexOf(columns[ck].id) >= 0) {
                        columns[ck].id = v4();
                    }

                    usedIds.push(columns[ck].id);

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (!blocks[bk].id || usedIds.indexOf(blocks[bk].id) >= 0) {
                            blocks[bk].id = v4();
                        }

                        usedIds.push(blocks[bk].id);
                    }
                }
            }
        }

        return structure;
    }

    static ensureStructureHasTypeInAllElements (structure) {
        for (let sk in structure.sections) {
            if (!structure.sections[sk].type) {
                structure.sections[sk].type = 'section';
            }

            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                if (!rows[rk].type) {
                    structure.sections[sk].rows[rk].type = 'row';
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (!columns[ck].type) {
                        structure.sections[sk].rows[rk].columns[ck].type = 'column';
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (!blocks[bk].type) {
                            structure.sections[sk].rows[rk].columns[ck].blocks[bk].type = 'block';
                        }
                    }
                }
            }
        }

        return structure;
    }

    static ensureColumnsHasSizesPropertyInStructure (structure) {
        for (let sk in structure.sections) {
            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (!columns[ck].sizes) {
                        structure.sections[sk].rows[rk].columns[ck].sizes = {
                            xxl: { size: null },
                            xl: { size: null },
                            lg: { size: null },
                            md: { size: null },
                            sm: { size: null },
                            xs: { size: null },
                        };
                    }
                }
            }
        }

        return structure;
    }
};
