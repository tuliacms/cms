<template>
    <div
        class="tued-structure-element-selectable"
        @mouseenter="$emit('selection-enter', 'block', block.id)"
        @mouseleave="$emit('selection-leave', 'block', block.id)"
        @mousedown.stop="selection.select('block', block.id)"
        data-tagname="Block"
    >
        <component
            :is="'block-' + block.code + '-editor'"
            :data="block.data"
            :id="block.id"
            @updated="eventDispatcher.emit('block.inner.updated')"
        ></component>
    </div>
</template>

<script>
export default {
    props: ['block', 'parent'],
    inject: ['selection', 'eventDispatcher'],
};
</script>
