import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class UnifiedToSource {
    static transform(unified) {
        return {
            sections: this.transformSections(unified.structure.sections, unified),
        };
    }

    static transformSections(sections, unified) {
        let result = [];

        for (let s in sections) {
            result.push({
                id: sections[s].id,
                store: {
                    data: ObjectCloner.deepClone(unified.data['sections'][sections[s].id] ?? {}),
                    config: ObjectCloner.deepClone(unified.config['sections'][sections[s].id] ?? {}),
                },
                rows: this.transformRows(sections[s].id, unified.structure.rows, unified),
            });
        }

        return result;
    }

    static transformRows(sectionId, rows, unified) {
        let result = [];

        for (let r in rows) {
            if (rows[r].parent !== sectionId) {
                continue;
            }

            result.push({
                id: rows[r].id,
                store: {
                    data: ObjectCloner.deepClone(unified.data['rows'][rows[r].id] ?? {}),
                    config: ObjectCloner.deepClone(unified.config['rows'][rows[r].id] ?? {}),
                },
                columns: this.transformColumns(rows[r].id, unified.structure.columns, unified),
            });
        }

        return result;
    }

    static transformColumns(rowId, columns, unified) {
        let result = [];

        for (let c in columns) {
            if (columns[c].parent !== rowId) {
                continue;
            }

            result.push({
                id: columns[c].id,
                store: {
                    data: ObjectCloner.deepClone(unified.data['columns'][columns[c].id] ?? {}),
                    config: ObjectCloner.deepClone(unified.config['columns'][columns[c].id] ?? {}),
                },
                blocks: this.transformBlocks(columns[c].id, unified.structure.blocks, unified),
            });
        }

        return result;
    }

    static transformBlocks(columnId, blocks, unified) {
        let result = [];

        for (let b in blocks) {
            if (blocks[b].parent !== columnId) {
                continue;
            }

            result.push({
                id: blocks[b].id,
                code: blocks[b].code,
                store: {
                    data: ObjectCloner.deepClone(unified.data['blocks'][blocks[b].id] ?? {}),
                    config: ObjectCloner.deepClone(unified.config['blocks'][blocks[b].id] ?? {}),
                },
            });
        }

        return result;
    }
}
