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
                        <div
                            v-for="(block, key) in column.blocks"
                            :key="'block-' + key"
                            class="tued-block"
                            :id="`tued-block-${block.id}`"
                        >
                            <component
                                :is="'block-' + block.code + '-render'"
                                :block="block"
                            ></component>
                        </div>
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
const SizesClassnameGenerator = require('shared/Structure/Columns/SizesClassnameGenerator.js').default;
const columnClass = (column) => {
    return (new SizesClassnameGenerator(
        column,
        ['tued-column']
    )).generate();
};
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
