<template>
    <div :class="{ 'tued-rendering-canvas': true }">
        <section
            v-for="(section, key) in structure.sections"
            :key="'section-' + key"
            class="tued-section"
            :id="`tued-section-${section.id}`"
        >
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
                            :is="'block-' + block.code + '-render'"
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
const { ref, defineProps, inject } = require('vue');
const props = defineProps(['structure']);
const messenger = inject('messenger');


/**********
 * Columns
 **********/
const ColumnSizesClassnameGenerator = require('shared/Structure/Columns/SizesClassnameGenerator.js').default;
const BlockSizingClassnameGenerator = require('shared/Structure/Blocks/SizingClassnameGenerator.js').default;
const blockClass = (block) => (new BlockSizingClassnameGenerator(block)).generate();
const columnClass = (column) => (new ColumnSizesClassnameGenerator(column, ['tued-column'])).generate();
const rowClass = (row, section) => {
    let classname = 'tued-row row';

    if (section.data.containerWidth === 'full-width-no-padding') {
        classname += ' g-0';
    }

    return classname;
};
const containerClass = (section) => {
    let classname = 'tued-container';

    if (section.data.containerWidth === 'full-width') {
        classname += ' container-fluid';
    } else if (section.data.containerWidth === 'default') {
        classname += ' container-xxl';
    }

    return classname;
};
</script>
