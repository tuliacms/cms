import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export default class StoreToUnifiedFormat {
    static transform(structureStore, configStoreRegistry, dataStoreRegistry) {
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

        this.transformStructure(unified, structureStore.export, configStoreRegistry, dataStoreRegistry);

        return unified;
    }

    static transformStructure(unified, structure, configStoreRegistry, dataStoreRegistry) {
        for (let s in structure.sections) {
            const section = structure.sections[s];
            this.transformSection(section, unified, configStoreRegistry, dataStoreRegistry);

            for (let r in section.rows) {
                const row = section.rows[r];
                this.transformRow(row, section, unified, configStoreRegistry, dataStoreRegistry);

                for (let c in row.columns) {
                    const column = row.columns[c];
                    this.transformColumn(column, row, unified, configStoreRegistry, dataStoreRegistry);

                    for (let b in column.blocks) {
                        this.transformBlock(column.blocks[b], column, unified, configStoreRegistry, dataStoreRegistry);
                    }
                }
            }
        }
    }

    static transformSection(section, unified, configStoreRegistry, dataStoreRegistry) {
        unified.structure.sections.push({
            id: section.id,
        });
    }

    static transformRow(row, section, unified, configStoreRegistry, dataStoreRegistry) {
        unified.structure.rows.push({
            id: row.id,
            parent: section.id,
        });
    }

    static transformColumn(column, row, unified, configStoreRegistry, dataStoreRegistry) {
        unified.structure.columns.push({
            id: column.id,
            parent: row.id,
        });
    }

    static transformBlock(block, column, unified, configStoreRegistry, dataStoreRegistry) {
        unified.structure.blocks.push({
            id: block.id,
            parent: column.id,
            code: block.code,
        });

        this.setConfig(unified, block.id, 'block', configStoreRegistry);
        this.setData(unified, block.id, 'block', dataStoreRegistry);
    }

    static setConfig(unified, id, type, configStoreRegistry) {
        const key = `${type}s`;

        if (!unified.config[key]) {
            unified.config[key] = {};
        }

        unified.config[key][id] = ObjectCloner.deepClone(configStoreRegistry.get(id, type).export);
    }

    static setData(unified, id, type, dataStoreRegistry) {
        const key = `${type}s`;

        if (!unified.data[key]) {
            unified.data[key] = {};
        }

        unified.data[key][id] = ObjectCloner.deepClone(dataStoreRegistry.get(id, type).export);
    }
}
