<template>
    <div :class="{ 'tued-container': true, 'tued-show-preview': renderPreview }">
        <Structure></Structure>
        <!--<RenderingCanvasComponent ref="renderedContent" :structure="structure"></RenderingCanvasComponent>-->
    </div>
</template>

<script setup>
import Structure from "editor/Structure/Structure.vue";
import { defineProps, ref, provide } from "vue";

const props = defineProps(['container']);
const renderPreview = ref(props.container.getParameter('options').show_preview_in_canvas);

const structure = props.container.get('structure');

provide('structure', structure);
provide('translator', props.container.get('translator'));
provide('messenger', props.container.get('messenger'));
provide('selection', props.container.get('selection'));
provide('eventBus', props.container.get('eventBus'));
provide('selection.selectedElementBoundaries', props.container.get('selection.selectedElementBoundaries'));
provide('usecase.selection', props.container.get('usecase.selection'));
</script>
<script>
export default {
    name: 'Tulia Editor - Editor',
}
</script>
