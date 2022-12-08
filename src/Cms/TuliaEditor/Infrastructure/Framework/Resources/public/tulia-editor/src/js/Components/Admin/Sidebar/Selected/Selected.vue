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
                <div :class="{
                    'tued-structure-selected-options': true,
                    'tued-structure-selected-active': selected.id === row.id && selected.type === 'row'
                }">
                    <div class="text-muted text-uppercase">{{ translator.trans('noEditOptionsForThisElement') }}</div>
                </div>
                <div
                    v-for="(column, key) in row.columns"
                    :key="'column-' + key"
                >
                    <div :class="{
                        'tued-structure-selected-options': true,
                        'tued-structure-selected-active': selected.id === column.id && selected.type === 'column'
                    }">
                        <div class="text-muted text-uppercase">{{ translator.trans('noEditOptionsForThisElement') }}</div>
                    </div>
                    <div
                        v-for="(block, key) in column.blocks"
                        :key="'block-' + key"
                    >
                        <div :class="{
                            'tued-structure-selected-options': true,
                            'tued-structure-selected-active': selected.id === block.id && selected.type === 'block'
                        }">
                            <div :class="{ 'd-block': existingBlocks[block.code], 'd-none': !existingBlocks[block.code] }">
                                <Block :block="block"></Block>
                                <component
                                    :is="'block-' + block.code + '-manager'"
                                    :block="block"
                                ></component>
                            </div>
                            <div :class="{ 'd-block': !existingBlocks[block.code], 'd-none': existingBlocks[block.code] }">
                                <div class="text-muted text-uppercase">{{ translator.trans('noEditOptionsForThisElement') }}</div>
                            </div>
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
const Block = require('components/Admin/Sidebar/Selected/Block.vue').default;
const { defineProps, reactive, inject, computed, onMounted } = require('vue');

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

const existingBlocks = {};

onMounted(() => {
    for (let i in TuliaEditor.blocks) {
        if (TuliaEditor.blocks[i].hasOwnProperty('manager')) {
            existingBlocks[TuliaEditor.blocks[i].code] = TuliaEditor.blocks[i].code;
        }
    }
});
</script>
