<template>
    <div :class="{ 'tued-container': true, 'tued-show-preview': renderPreview }">
        <Structure></Structure>
        <Canvas ref="renderedContent"></Canvas>
    </div>
</template>

<script setup>
import Structure from "editor/Structure/Structure.vue";
import Canvas from "editor/Rendering/Canvas.vue";
import { defineProps, ref, provide, onMounted } from "vue";

const props = defineProps(['container']);
const renderPreview = ref(props.container.getParameter('options').show_preview_in_canvas);

const structure = props.container.get('structure.store');

provide('options', props.container.getParameter('options'));
provide('translator', props.container.get('translator'));
provide('messenger', props.container.get('messenger'));
provide('eventBus', props.container.get('eventBus'));
provide('structure.store', structure);
provide('selection.store', props.container.get('selection.store'));
provide('selection.selectedElementBoundaries', props.container.get('selection.selectedElementBoundaries'));
provide('selection.hoveredElementBoundaries', props.container.get('selection.hoveredElementBoundaries'));
provide('selection.hoveredElementResolver', props.container.get('selection.hoveredElementResolver'));
provide('usecase.selection', props.container.get('usecase.selection'));
provide('usecase.contentRendering', props.container.get('usecase.contentRendering'));
provide('contextmenu', props.container.get('contextmenu'));
provide('instance.blocks', props.container.get('instantiator.block'));
provide('instance.columns', props.container.get('instantiator.column'));
provide('instance.rows', props.container.get('instantiator.row'));
provide('instance.sections', props.container.get('instantiator.section'));
provide('instance.extensions', props.container.get('instantiator.extension'));
provide('blocks.registry', props.container.get('blocks.registry'));
provide('extensions.registry', props.container.get('extensions.registry'));
provide('controls.registry', props.container.get('controls.registry'));

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
