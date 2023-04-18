import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class SourceToUnifiedFormat {
    static transform(source) {
        const unified = {
            structure: {
                sections: [],
                rows: [],
                columns: [],
                blocks: [],
            },
            data: {
                sections: {},
                rows: {},
                columns: {},
                blocks: {},
            },
            config: {
                sections: {},
                rows: {},
                columns: {},
                blocks: {},
            },
        };

        this.transformStructure(source, unified);

        return unified;
    }

    static transformStructure(source, unified) {
        for (let s in source?.sections ?? []) {
            const section = source.sections[s];
            this.transformSection(section, unified);

            for (let r in section?.rows ?? []) {
                const row = section.rows[r];
                this.transformRow(row, section, unified);

                for (let c in row?.columns ?? []) {
                    const column = row.columns[c];
                    this.transformColumn(column, row, unified);

                    for (let b in column?.blocks ?? []) {
                        this.transformBlock(column.blocks[b], column, unified);
                    }
                }
            }
        }
    }

    static transformSection(section, unified) {
        unified.structure.sections.push({
            id: section.id,
        });
    }

    static transformRow(row, section, unified) {
        unified.structure.rows.push({
            id: row.id,
            parent: section.id,
        });
    }

    static transformColumn(column, row, unified) {
        unified.structure.columns.push({
            id: column.id,
            parent: row.id,
        });
    }

    static transformBlock(block, column, unified) {
        unified.structure.blocks.push({
            id: block.id,
            parent: column.id,
            code: block.code,
        });
        this.setConfig(unified, block.id, 'block', block.store.config);
        this.setData(unified, block.id, 'block', block.store.data);
    }

    static setConfig(unified, id, type, config) {
        const key = `${type}s`;

        if (!unified.config[key]) {
            unified.config[key] = {};
        }

        unified.config[key][id] = ObjectCloner.deepClone(config);
    }

    static setData(unified, id, type, data) {
        const key = `${type}s`;

        if (!unified.data[key]) {
            unified.data[key] = {};
        }

        unified.data[key][id] = ObjectCloner.deepClone(data);
    }
}
