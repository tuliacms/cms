<template>
    <div class="tued-structure-element tued-structure-element-block">
        <div
            :class="{ 'tued-label': true, 'tued-element-selected': selectionStore.selected.id === block.id, 'tued-element-hovered': selectionStore.hovered.id === block.id }"
            @dblclick.stop="emit('selected')"
            @mouseenter="selectionUseCase.hover(block.id, 'block')"
            @mouseleave="selectionUseCase.dehover()"
            @click.stop="selectionUseCase.select(block.id, 'block')"
            @contextmenu="selectionUseCase.select(block.id, 'block')"
            :tued-contextmenu="contextmenu.register(block.id, 'block')"
        >
            <div class="tued-structure-draggable-handler" @mousedown.stop="selectionUseCase.select(block.id, 'block')">
                <i class="fas fa-arrows-alt"></i>
            </div>
            <span>{{ block.details.name }}</span>
        </div>
    </div>
</template>

<script setup>
import { inject, defineEmits, defineProps } from "vue";

const props = defineProps(['parent', 'block']);
const emit = defineEmits(['draggable-start', 'draggable-change', 'draggable-end', 'selected']);
const translator = inject('translator');
const structureDragOptions = inject('structureDragOptions');
const selectionUseCase = inject('usecase.selection');
const selectionStore = inject('selection.store');
const contextmenu = inject('usecase.contextmenu');
const block = inject('structure').block(props.block);
</script>
