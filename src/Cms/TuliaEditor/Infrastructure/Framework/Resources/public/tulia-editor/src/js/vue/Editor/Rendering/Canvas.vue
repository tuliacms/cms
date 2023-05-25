<template>
    <div :class="{ 'tued-rendering-canvas': true }" ref="renderedContent">
        <section
            v-for="(section, key) in structure.sections"
            :key="'section-' + key"
            class="tued-section"
            :id="`tued-section-${section.id}`"
        >
            <div v-if="section.config.anchorId" :id="section.config.anchorId" class="tued-section-anchor"></div>
            <div :class="containerClass(section)">
                <div
                    v-for="(row, key) in section.rows"
                    :key="'row-' + key"
                    :class="rowClass(row, section)"
                    :id="`tued-row-${row.id}`"
                >
                    <div
                        v-for="(column, key) in row.columns"
                        :key="'column-' + key"
                        :class="columnClass(column)"
                        :id="`tued-column-${column.id}`"
                    >
                        <component
                            v-for="(block, key) in column.blocks"
                            :key="'block-' + key"
                            :is="registry.getComponentName(block.details.code, 'render')"
                            :block="block"
                            :id="`tued-block-${block.id}`"
                            :class="blockClass(block)"
                        ></component>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script setup>
import {computed, inject, ref} from "vue";
import ColumnClassnameGenerator from "core/Editor/Render/Column/ColumnClassnameGenerator";
import BlockClassnameGenerator from "core/Editor/Render/Block/BlockClassnameGenerator";
import RowClassnameGenerator from "core/Editor/Render/Row/RowClassnameGenerator";
import ContainerClassnameGenerator from "core/Editor/Render/Section/ContainerClassnameGenerator";

const messenger = inject('messenger');
const structureStore = inject('structure.store');
const structureService = inject('structure');
const registry = inject('blocks.registry');

const renderedContent = ref(null);

inject('usecase.contentRendering').setNodeReference(renderedContent);

const blockClass = (block) => BlockClassnameGenerator.generate(block);
const columnClass = (column) => ColumnClassnameGenerator.generate(column, ['tued-column']);
const rowClass = (row, section) => RowClassnameGenerator.generate(row, section);
const containerClass = (section) => ContainerClassnameGenerator.generate(section);

const structure = computed(() => {
    const structure = {
        sections: [],
    };

    for (let s in structureStore.sections) {
        const section = structureService.section(structureStore.sections[s].id);
        section.rows = [];

        const rows = structureStore.rowsOf(section.id);

        for (let r in rows) {
            const row = structureService.row(rows[r].id);
            row.columns = [];

            const columns = structureStore.columnsOf(row.id);

            for (let c in columns) {
                const column = structureService.column(columns[c].id);
                column.blocks = [];

                const blocks = structureStore.blocksOf(column.id);

                for (let b in blocks) {
                    column.blocks.push(structureService.block(blocks[b].id));
                }

                row.columns.push(column);
            }

            section.rows.push(row);
        }

        structure.sections.push(section);
    }

    return structure;
});
</script>
