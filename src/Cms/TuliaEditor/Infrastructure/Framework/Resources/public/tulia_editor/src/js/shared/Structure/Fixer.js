const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const { v4 } = require('uuid');

export default class Fixer {
    fix (structure) {
        let workingCopy = ObjectCloner.deepClone(structure);

        workingCopy = this.ensureAllIdsAreUnique(workingCopy);
        workingCopy = this.ensureStructureHasTypeInAllElements(workingCopy);
        workingCopy = this.ensureColumnsHasSizesPropertyInStructure(workingCopy);
        workingCopy = this.ensureElementsHasMetadataPropertyInStructure(workingCopy);
        workingCopy = this.ensureElementsHasParentPropertyInStructure(workingCopy);

        return workingCopy;
    }

    ensureAllIdsAreUnique (structure) {
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

    ensureStructureHasTypeInAllElements (structure) {
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

    ensureColumnsHasSizesPropertyInStructure (structure) {
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

    ensureElementsHasMetadataPropertyInStructure (structure) {
        for (let sk in structure.sections) {
            if (!structure.sections[sk].metadata) {
                structure.sections[sk].metadata = {
                    hovered: false,
                    selected: false,
                };
            }

            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                if (!rows[rk].metadata) {
                    structure.sections[sk].rows[rk].metadata = {
                        hovered: false,
                        selected: false,
                    };
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (!columns[ck].metadata) {
                        structure.sections[sk].rows[rk].columns[ck].metadata = {
                            hovered: false,
                            selected: false,
                        };
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (!blocks[bk].metadata) {
                            structure.sections[sk].rows[rk].columns[ck].blocks[bk].metadata = {
                                hovered: false,
                                selected: false,
                            };
                        }
                    }
                }
            }
        }

        return structure;
    }

    ensureElementsHasParentPropertyInStructure (structure) {
        for (let sk in structure.sections) {
            if (!structure.sections[sk].metadata.parent) {
                structure.sections[sk].metadata.parent = {
                    type: null,
                    id: null,
                };
            }

            let rows = structure.sections[sk].rows;

            for (let rk in rows) {
                if (!rows[rk].metadata.parent) {
                    structure.sections[sk].rows[rk].metadata.parent = {
                        type: 'section',
                        id: structure.sections[sk].id,
                    };
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (!columns[ck].metadata.parent) {
                        structure.sections[sk].rows[rk].columns[ck].metadata.parent = {
                            type: 'row',
                            id: structure.sections[sk].rows[rk].id,
                        };
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (!blocks[bk].metadata.parent) {
                            structure.sections[sk].rows[rk].columns[ck].blocks[bk].metadata.parent = {
                                type: 'column',
                                id: structure.sections[sk].rows[rk].columns[ck].id,
                            };
                        }
                    }
                }
            }
        }

        return structure;
    }
}
