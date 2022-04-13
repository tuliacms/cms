<template>
    <div class="tued-container">
        <StructureComponent :structure="structure"></StructureComponent>
        <RenderingCanvasComponent ref="renderedContent" :structure="structure"></RenderingCanvasComponent>
    </div>
</template>

<script setup>
const StructureComponent = require("components/Editor/Structure/Structure.vue").default;
const RenderingCanvasComponent = require("components/Editor/Rendering/Canvas.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const Selection = require("shared/Structure/Selection/Selection.js").default;
const Structure = require('shared/Structure.js').default;
const { defineProps, provide, reactive, onMounted, toRaw, ref } = require('vue');

const props = defineProps([
    'container',
    'instanceId',
    'options',
    'availableBlocks',
    'structure'
]);

const structure = reactive(ObjectCloner.deepClone(props.structure));
const selection = new Selection(structure, props.container.messenger);

provide('selection', selection);
provide('messenger', props.container.messenger);
provide('eventDispatcher', props.container.eventDispatcher);

const renderedContent = ref(null);

onMounted(() => {
    props.container.eventDispatcher.on('block.inner.updated', () => {
        props.container.messenger.send('structure.synchronize.from.editor', ObjectCloner.deepClone(toRaw(structure)));
    });

    props.container.messenger.listen('structure.rendered.fetch', () => {
        props.container.messenger.send(
            'structure.rendered.data',
            renderedContent.value.$el.innerHTML,
            ObjectCloner.deepClone(toRaw(structure))
        );
    });
    props.container.messenger.listen('structure.synchronize.from.admin', (newStructure) => {
        structure.sections = newStructure.sections;
        props.container.messenger.send('structure.updated');
    });

    props.container.messenger.listen('structure.move-element', (delta) => {
        Structure.moveElementUsingDelta(structure, delta);
        selection.resetHovered();
        selection.select(delta.element.type, delta.element.id);
    });

    props.container.messenger.listen('editor.click.outside', () => {
        selection.resetSelection();
    });

    document.addEventListener('click', (event) => {
        if (event.target.tagName === 'HTML') {
            props.container.messenger.send('editor.click.outside');
        }
    });
});
</script>

