<template>
    <div class="tued-rendering-canvas" as="asd">
        <section
            v-for="(section, key) in structure.sections"
            :key="'section-' + key"
            class="tued-section"
        >
            <div class="tued-container container-xxl">
                <div
                    v-for="(row, key) in section.rows"
                    :key="'row-' + key"
                    class="tued-row row"
                >
                    <div
                        v-for="(column, key) in row.columns"
                        :key="'column-' + key"
                        class="tued-column col"
                    >
                        <component
                            v-for="(block, key) in column.blocks"
                            :key="'block-' + key"
                            :is="block.type + '-rendering-component-frame'"
                            :blockData="block.data"
                        ></component>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
export default {
    name: 'RenderingCanvas',
    props: ['structure'],
};
</script>
