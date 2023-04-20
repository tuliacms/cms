<template>
    <div>
        <vuedraggable
            group="blocks"
            item-key="id"
            :list="structureStore.blocksOf(props.parent)"
            tag="div"
            :component-data="{ class: 'tued-structure-draggable-group', name: 'fade', as: 'transition-group', 'data-draggable-delta-transformer-parent': `${parent.type}.${parent.id}` }"
            v-bind="structureDragOptions"
            handle=".tued-structure-element-block > .tued-label > .tued-structure-draggable-handler"
            @start="(event) => emit('draggable-start', event)"
            @change="(event) => emit('draggable-change', event)"
            @end="(event) => emit('draggable-end', event)"
        >
            <template #item="{element}">
                <Block
                    data-element-type="block"
                    :data-element-id="element.id"
                    :block="element"
                    @selected="emit('selected')"
                ></Block>
            </template>
        </vuedraggable>
        <div class="tued-structure-new-element" @click="blocksPicker.newAt(props.parent)">
            {{ translator.trans('newBlock') }}
        </div>
    </div>
</template>

<script setup>
import vuedraggable from "vuedraggable/src/vuedraggable";
import Block from "admin/Sidebar/Block.vue";
import { inject, defineEmits, defineProps, onMounted } from "vue";

const props = defineProps(['parent']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end', 'selected']);
const translator = inject('translator');
const structureDragOptions = inject('structureDragOptions');
const structureStore = inject('structure.store');
const contextmenu = inject('usecase.contextmenu');
const blocksPicker = inject('blocks.picker');
const blocksUsecase = inject('usecase.blocks');

onMounted(() => {
    contextmenu.items('blocks', 'column', () => {
        return [
            {
                onClick: (id) => blocksPicker.newAt(id),
                label: translator.trans('addBlock'),
                icon: 'fas fa-plus',
            },
        ];
    });
    contextmenu.items('blocks', 'block', () => {
        return [
            {
                group: 'block',
                onClick: (id) => blocksUsecase.remove(id),
                label: translator.trans('delete'),
                icon: 'fas fa-trash',
                classname: 'dropdown-item-danger',
            },
        ];
    });
});
</script>
