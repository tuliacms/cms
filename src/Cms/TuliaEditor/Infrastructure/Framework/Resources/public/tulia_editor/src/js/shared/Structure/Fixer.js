const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const { v4 } = require('uuid');

export default class Fixer {
    usedIds = [];

    fixStructure (structure) {
        let workingCopy = ObjectCloner.deepClone(structure);

        for (let i in workingCopy.sections) {
            workingCopy.sections[i] = this.fixSection(workingCopy.sections[i]);
        }

        return workingCopy;
    }

    fixSection (section) {
        this.fixElementId(section);

        if (!section.type) {
            section.type = 'section';
        }

        if (!section.metadata) {
            section.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: null,
                    id: null,
                }
            };
        }

        if (!section.rows || section.rows.length === 0) {
            section.rows = [{}];
        }

        for (let i in section.rows) {
            section.rows[i] = this.fixRow(section.rows[i]);
        }

        return section;
    }

    fixRow (row, sectionId) {
        this.fixElementId(row);

        if (!row.type) {
            row.type = 'row';
        }

        if (!row.metadata) {
            row.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: 'section',
                    id: sectionId,
                }
            };
        }

        if (!row.columns || row.columns.length === 0) {
            row.columns = [{}];
        }

        for (let i in row.columns) {
            row.columns[i] = this.fixColumn(row.columns[i]);
        }

        return row;
    }

    fixColumn (column, rowId) {
        this.fixElementId(column);

        if (!column.type) {
            column.type = 'column';
        }

        if (!column.sizes) {
            column.sizes = {
                xxl: { size: null },
                xl: { size: null },
                lg: { size: null },
                md: { size: null },
                sm: { size: null },
                xs: { size: null },
            };
        }

        if (!column.metadata) {
            column.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: 'row',
                    id: rowId,
                }
            };
        }

        if (!column.blocks) {
            column.blocks = [];
        }

        for (let i in column.blocks) {
            column.blocks[i] = this.fixBlock(column.blocks[i]);
        }

        return column;
    }

    fixBlock (block, columnId) {
        this.fixElementId(block);

        if (!block.type) {
            block.type = 'block';
        }

        if (!block.metadata) {
            block.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: 'column',
                    id: columnId,
                }
            };
        }

        if (!block.data) {
            block.data = {};
        }

        return block;
    }

    fixElementId (element) {
        if (!element.id || this.usedIds.indexOf(element.id) >= 0) {
            element.id = v4();
        }

        this.usedIds.push(element.id);
    }
}
