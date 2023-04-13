<template>
    <div class="tued-sidebar-selected-element">
        <div
            v-for="(section, key) in structureStore.sections"
            :key="'section-' + key"
        >
            <div :class="{
                'tued-structure-selected-options': true,
                'tued-structure-selected-active': selectionStore.selected.id === section.id && selectionStore.selected.type === 'section'
            }">
                <Section :section="section"></Section>
            </div>
            <div
                v-for="(row, key) in structureStore.rowsOf(section.id)"
                :key="'row-' + key"
            >
                <div :class="{
                    'tued-structure-selected-options': true,
                    'tued-structure-selected-active': selectionStore.selected.id === row.id && selectionStore.selected.type === 'row'
                }">
                    <div class="text-muted text-uppercase">{{ translator.trans('noEditOptionsForThisElement') }}</div>
                </div>
                <div
                    v-for="(column, key) in structureStore.columnsOf(row.id)"
                    :key="'column-' + key"
                >
                    <div :class="{
                        'tued-structure-selected-options': true,
                        'tued-structure-selected-active': selectionStore.selected.id === column.id && selectionStore.selected.type === 'column'
                    }">
                        <Column :column="column"></Column>
                    </div>
                    <div
                        v-for="(block, key) in column.blocks"
                        :key="'block-' + key"
                    >
                        <div :class="{
                            'tued-structure-selected-options': true,
                            'tued-structure-selected-active': selectionStore.selected.id === block.id && selectionStore.selected.type === 'block'
                        }">
                            <Block :block="block"></Block>
                            <div :class="{ 'd-block': registry.hasComponent(block.code, 'manager'), 'd-none': !registry.hasComponent(block.code, 'manager') }">
                                <component
                                    :is="registry.getComponentName(block.code, 'manager')"
                                    :block="block"
                                ></component>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-if="!selectionStore.selected.id && !selectionStore.selected.type" class="tued-sidebar-selected-element-not-selected">
            {{ translator.trans('selectEditableElementToShowOptions') }}
        </div>
    </div>
</template>

<script setup>
import Section from "admin/Sidebar/Selected/Section.vue";
import Column from "admin/Sidebar/Selected/Column.vue";
import Block from "admin/Sidebar/Selected/Block.vue";
import { inject } from "vue";

const selectionStore = inject('selection.store');
const structureStore = inject('structure.store');
const translator = inject('translator');
const registry = inject('blocks.registry');
</script>
