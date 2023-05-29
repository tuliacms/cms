<template>
    <div
        class="tued-structure-element-selectable"
        @mouseenter="$emit('selection-enter', 'block', block.id)"
        @mouseleave="$emit('selection-leave', 'block', block.id)"
        @mousedown.stop="selection.select('block', block.id, 'editor')"
        data-tagname="Block"
        :tued-contextmenu="contextmenu.register('block', block.id)"
    >
        <component
            :is="'block-' + block.code + '-editor'"
            :block="block"
            @updated="messenger.notify('structure.element.updated', block.id)"
            :tued-contextmenu="contextmenu.register(`block-${block.id}`, block.id)"
            :class="blockClass(block)"
        ></component>
    </div>
</template>

<script setup>
const { defineProps, inject, computed } = require('vue');
const Block = require('./Block.vue').default;
const BlockSizingClassnameGenerator = require('shared/Structure/Blocks/SizingClassnameGenerator.js').default;

const props = defineProps(['block', 'parent']);
const selection = inject('selection');
const messenger = inject('messenger');
const contextmenu = inject('contextmenu');

const blockClass = (block) => (new BlockSizingClassnameGenerator(block)).generate();
</script>
