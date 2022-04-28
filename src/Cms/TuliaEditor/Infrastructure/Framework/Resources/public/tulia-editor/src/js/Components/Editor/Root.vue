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
const StyleCompiler = require('shared/Structure/Style/Compiler.js').default;
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
provide('translator', props.container.translator);
provide('structureManipulator', structureManipulator);
provide('options', props.options);

const renderedContent = ref(null);

onMounted(() => {
    props.container.messenger.operation('structure.fetch', (params, success, fail) => {
        let rawStructure = ObjectCloner.deepClone(toRaw(structure));

        success({
            content: renderedContent.value.$el.innerHTML,
            structure: rawStructure,
            style: (new StyleCompiler(structure)).compile()
        });
    });
    props.container.messenger.on('structure.synchronize.from.admin', (newStructure) => {
        structure.sections = newStructure.sections;
        props.container.messenger.notify('structure.updated');
    });

    props.container.messenger.on('structure.move-element', (delta) => {
        structureManipulator.moveElementUsingDelta(delta);

        // @todo We need mechanism of wating for all windows confirms the message was handled and operation of moving was done in structure.
        // Only with that we can select element in document.
        // Right now we have to hack system, and select with timeout.
        setTimeout(() => {
            selection.resetHovered();
            selection.select(delta.element.type, delta.element.id, 'sidebar');
        }, 60);
    });

    props.container.messenger.on('editor.click.outside', () => {
        selection.resetSelection();
    });

    document.addEventListener('click', (event) => {
        if (event.target.tagName === 'HTML') {
            props.container.messenger.notify('editor.click.outside');
        }
    });
});





/*********
 * Blocks
 *********/
const Blocks = require('shared/Structure/Blocks/Blocks.js').default;
const BlocksRegistry = require("shared/Structure/Blocks/Registry.js").default;

const blocksRegistry = new BlocksRegistry(props.availableBlocks);

provide('blocksRegistry', blocksRegistry);
provide('blocks', new Blocks(props.options.blocks, props.container.messenger));
</script>

