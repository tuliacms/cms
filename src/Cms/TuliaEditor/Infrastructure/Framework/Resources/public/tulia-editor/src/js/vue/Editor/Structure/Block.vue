<template>
    <div
        class="tued-structure-element-selectable"
        :id="'tued-structure-block-' + block.id"
        @mouseenter="emit('selection-enter', block.id, 'block')"
        @mouseleave="emit('selection-leave', block.id, 'block')"
        @mousedown.stop="selection.select(block.id, 'block', true)"
        data-tagname="Block"
        :tued-contextmenu="contextmenu.register(block.id, 'block')"
    >
        <component
            :is="registry.getComponentName(block.details.code, 'editor')"
            :block="block"
            :class="blockClass(block)"
        ></component>
    </div>
</template>

<script setup>
import BlockClassnameGenerator from "core/Editor/Render/Block/BlockClassnameGenerator";
import { inject, defineProps, defineEmits } from "vue";

const props = defineProps(['parent', 'block']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const structure = inject('structure.store');
const contextmenu = inject('contextmenu');
const translator = inject('translator');
const selection = inject('usecase.selection');
const registry = inject('blocks.registry');

const block = inject('structure').block(props.block);
const blockClass = (block) => BlockClassnameGenerator.generate(block);
</script>
