<template>
    <div class="tued-sidebar-selected-element">
        <div
            v-for="(section, key) in structure.sections"
            :key="'section-' + key"
        >
            <div :class="{
                'tued-structure-selected-options': true,
                'tued-structure-selected-active': selected.id === section.id && selected.type === 'section'
            }">
                <Section :section="section"></Section>
            </div>
            <div
                v-for="(row, key) in section.rows"
                :key="'row-' + key"
            >
                <div
                    v-for="(column, key) in row.columns"
                    :key="'column-' + key"
                >
                    <div
                        v-for="(block, key) in column.blocks"
                        :key="'block-' + key"
                    >
                        <div :class="{
                            'tued-structure-selected-options': true,
                            'tued-structure-selected-active': selected.id === block.id && selected.type === 'block'
                        }">
                            <component
                                :is="'block-' + block.code + '-manager'"
                                :block="block"
                            ></component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!selected.id && !selected.type" class="tued-sidebar-selected-element-not-selected">
            {{ translator.trans('selectEditableElementToShowOptions') }}
        </div>
    </div>
</template>

<script setup>
const Section = require('components/Admin/Sidebar/Selected/Section.vue').default;
const { defineProps, reactive, inject } = require('vue');

const messenger = inject('messenger');
const translator = inject('translator');
const props = defineProps(['structure']);
const structure = reactive(props.structure);

const selected = reactive({
    id: null,
    type: null
});

messenger.on('structure.selection.selected', (type, id) => {
    selected.id = id;
    selected.type = type;
});
messenger.on('structure.selection.deselected', () => {
    selected.id = null;
    selected.type = null;
});
</script>
