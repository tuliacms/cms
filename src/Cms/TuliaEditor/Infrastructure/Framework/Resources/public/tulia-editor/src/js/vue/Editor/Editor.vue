<template>
    <div :class="{ 'tued-container': true, 'tued-show-preview': renderPreview }">
        <Structure></Structure>
        <!--<RenderingCanvasComponent ref="renderedContent" :structure="structure"></RenderingCanvasComponent>-->
    </div>
</template>

<script setup>
import Structure from "editor/Structure/Structure.vue";
import { defineProps, ref, provide, onMounted } from "vue";

const props = defineProps(['container']);
const renderPreview = ref(props.container.getParameter('options').show_preview_in_canvas);

const structure = props.container.get('structure.store');

provide('translator', props.container.get('translator'));
provide('messenger', props.container.get('messenger'));
provide('eventBus', props.container.get('eventBus'));
provide('structure.store', structure);
provide('selection.store', props.container.get('selection.store'));
provide('selection.selectedElementBoundaries', props.container.get('selection.selectedElementBoundaries'));
provide('selection.hoveredElementBoundaries', props.container.get('selection.hoveredElementBoundaries'));
provide('selection.hoveredElementResolver', props.container.get('selection.hoveredElementResolver'));
provide('usecase.selection', props.container.get('usecase.selection'));
provide('contextmenu', props.container.get('contextmenu'));
provide('instance.blocks', props.container.get('instantiator').instantiator('block'));
provide('instance.columns', props.container.get('instantiator').instantiator('column'));
provide('instance.rows', props.container.get('instantiator').instantiator('row'));
provide('instance.sections', props.container.get('instantiator').instantiator('section'));

const contextmenu = props.container.get('usecase.contextmenu');

onMounted(() => {
    document.addEventListener('click', event => contextmenu.hide());
    document.addEventListener('contextmenu', event => contextmenu.openUsingEvent(event));
});
</script>
<script>
export default {
    name: 'Tulia Editor - Editor',
}
</script>
