<template>
    <div
        class="tued-structure-element-selectable"
        @mouseenter="$emit('selection-enter', 'block', block.id)"
        @mouseleave="$emit('selection-leave', 'block', block.id)"
        @mousedown.stop="selection.select('block', block.id)"
        data-tagname="Block"
    >
        <component
            :is="'block-' + block.block_type + '-editor'"
            :data="block.data"
        ></component>
    </div>
</template>

<script>
export default {
    props: ['block', 'parent'],
    inject: ['selection'],
};
</script>
