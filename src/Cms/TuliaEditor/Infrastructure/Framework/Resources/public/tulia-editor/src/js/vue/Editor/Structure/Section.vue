<template>
    <section
        :id="'tued-structure-section-' + section.id"
        class="tued-structure-section tued-structure-element-selectable"
        data-tagname="Section"
        @mousedown.stop="selection.select(section.id, 'section')"
        @mouseenter="emit('selection-enter', section.id, 'section')"
        @mouseleave="emit('selection-leave', section.id, 'section')"
        :tued-contextmenu="contextmenu.register(section.id, 'section')"
    >
        <div :class="containerClassname">
            <Rows
                :parent="section.id"
                @selection-enter="(id, type) => $emit('selection-enter', id, type)"
                @selection-leave="(id, type) => $emit('selection-leave', id, type)"
            ></Rows>
        </div>
        <div
            class="tued-structure-empty-element"
            v-if="structure.rowsOf(section.id).length === 0"
        >
            <span>{{ translator.trans('emptySection') }}</span>
        </div>
    </section>
</template>

<script setup>
import { defineProps, defineEmits, inject, computed } from "vue";
import Rows from "editor/Structure/Rows.vue";

const props = defineProps(['section']);
const emit = defineEmits(['selection-enter', 'selection-leave']);
const translator = inject('translator');
const selection = inject('usecase.selection');
const contextmenu = inject('contextmenu');
const structure = inject('structure.store');
const section = inject('instance.sections').editor(props);

const containerClassname = computed(() => {
    switch (section.config.containerWidth) {
        case 'full-width': return 'container-fluid';
        case 'full-width-no-padding': return '';
        default: return 'container-xxl';
    }
});
</script>
