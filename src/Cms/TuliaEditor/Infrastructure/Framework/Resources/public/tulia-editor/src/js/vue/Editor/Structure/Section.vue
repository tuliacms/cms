<template>
    <!--
        @mouseenter="$emit('selection-enter', 'section', section.id)"
        @mouseleave="$emit('selection-leave', 'section', section.id)"
        :tued-contextmenu="contextmenu.register('section', section.id)"
    -->
    <section
        class="tued-structure-section tued-structure-element-selectable"
        :id="section.id"
        data-tagname="Section"
        @mousedown.stop="selection.select(section.id, 'section')"
    >
<!--        <div :class="containerClassname">
            <Row
                v-for="row in props.section.rows"
                :id="'tued-structure-row-' + row.id"
                :key="row.id"
                :row="row"
                :parent="section"
                @selection-enter="(type, id) => $emit('selection-enter', type, id)"
                @selection-leave="(type, id) => $emit('selection-leave', type, id)"
            ></Row>
        </div>-->
        <div class="tued-structure-empty-element">
            <span>{{ translator.trans('emptySection') }} {{ section.id }}</span>
        </div>
<!--        <div
            class="tued-structure-empty-element"
            v-if="props.section.rows.length === 0"
        >
            <span>{{ translator.trans('emptySection') }}</span>
        </div>-->
    </section>
</template>

<script setup>
import { defineProps, inject } from "vue";

const props = defineProps(['section']);
const translator = inject('translator');
const selection = inject('usecase.selection');

/*const Row = require('./Row.vue').default;
const { defineProps, inject, computed } = require('vue');
const props = defineProps(['section']);
const section = inject('sections.instance').editor(props);
const selection = inject('selection');
const translator = inject('translator');
const contextmenu = inject('contextmenu');

const containerClassname = computed(() => {
    switch (section.data.containerWidth) {
        case 'full-width': return 'container-fluid';
        case 'full-width-no-padding': return '';
        default: return 'container-xxl';
    }
});*/
</script>
