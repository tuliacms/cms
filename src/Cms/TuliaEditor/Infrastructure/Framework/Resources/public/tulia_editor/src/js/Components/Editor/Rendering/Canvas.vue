<template>
    <div :class="{ 'tued-rendering-canvas': true, 'tued-rendering-canvas-showed': renderPreview }">
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
                            :is="'block-' + block.code + '-render'"
                            :data="block.data"
                            :id="block.id"
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

/**********************
 * Render area preview
 **********************/
const renderPreview = ref(false);
messenger.operation('editor.canvas.preview.toggle', (params, success, fail) => {
    renderPreview.value = !renderPreview.value;

    success();
});
</script>
