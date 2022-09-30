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
        ></component>
    </div>
</template>

<script>
export default {
    props: ['block', 'parent'],
    inject: ['selection', 'messenger', 'contextmenu'],
};
</script>
