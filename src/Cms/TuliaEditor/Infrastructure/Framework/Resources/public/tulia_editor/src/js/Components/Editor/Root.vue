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
const StructureManipulator = require('shared/Structure/StructureManipulator.js').default;
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
const structureManipulator = new StructureManipulator(structure, props.container.messenger);

provide('selection', selection);
provide('messenger', props.container.messenger);
provide('eventDispatcher', props.container.eventDispatcher);
provide('structureManipulator', structureManipulator);

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
        structureManipulator.moveElementUsingDelta(delta);

        // @todo We need mechanism of wating for all windows confirms the message was handled and operation of moving was done in structure.
        // Only with that we can select element in document.
        // Right now we have to hack system, and select with timeout.
        setTimeout(() => {
            selection.resetHovered();
            selection.select(delta.element.type, delta.element.id);
        }, 60);
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

