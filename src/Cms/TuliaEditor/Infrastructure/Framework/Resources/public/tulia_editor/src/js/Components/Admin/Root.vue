<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <CanvasComponent
                :editorView="options.editor.view + '?tuliaEditorInstance=' + instanceId"
                :canvasOptions="canvasOptions"
            ></CanvasComponent>
            <SidebarComponent
                :structure="structure"
            ></SidebarComponent>
            <BlockPickerComponent
                :availableBlocks="availableBlocks"
                :blockPickerData="blockPickerData"
            ></BlockPickerComponent>
        </div>
    </div>
</template>

<script setup>
const CanvasComponent = require("components/Admin/Canvas/Canvas.vue").default;
const SidebarComponent = require("components/Admin/Sidebar/Sidebar.vue").default;
const BlockPickerComponent = require("components/Admin/Block/PickerModal.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const { defineProps, provide, reactive, onMounted, isProxy, toRaw } = require('vue');

const props = defineProps([
    'editor',
    'container',
    'instanceId',
    'options',
    'availableBlocks',
    'canvas',
    'structure'
]);

provide('messenger', props.container.messenger);
provide('translator', props.container.translator);
provide('eventDispatcher', props.container.eventDispatcher);
provide('options', props.options);

onMounted(() => {
    props.container.eventDispatcher.on('editor.save', () => {
        props.container.messenger.on('structure.rendered.data', (content, newStructure) => {
            selection.resetHovered();
            selection.resetSelection();
            structure.sections = newStructure.sections;
            useCurrentStructureAsPrevious();
            props.editor.updateContent(newStructure, content);
            props.editor.closeEditor();
            props.container.messenger.send('editor.save');
        });

        props.container.messenger.send('structure.rendered.fetch');
    });
    props.container.eventDispatcher.on('editor.cancel', () => {
        selection.resetHovered();
        selection.resetSelection();
        restorePreviousStructure();
        props.container.messenger.send('structure.synchronize.from.admin', ObjectCloner.deepClone(toRaw(structure)));
        props.editor.closeEditor();
        props.container.messenger.send('editor.cancel');
    });
});




/*************
 * Structure *
 *************/
const StructureManipulator = require('shared/Structure/StructureManipulator.js').default;
const Selection = require("shared/Structure/Selection/Selection.js").default;

// 'structure' store structure live updated from Editor iframe instance.
// Default value of this field is a value from 'options' passed in new instance creation.
const structure = reactive(props.structure);
let previousStructure = ObjectCloner.deepClone(props.structure);

const selection = new Selection(structure, props.container.messenger);
const structureManipulator = new StructureManipulator(structure, props.container.messenger);

provide('selection', selection);
provide('structureManipulator', structureManipulator);

onMounted(() => {
    props.container.messenger.on('structure.updated', () => {
        selection.update();
    });

    props.container.messenger.on('structure.synchronize.from.editor', (newStructure) => {
        structureManipulator.update(newStructure);
    });

    props.container.messenger.on('structure.element.created', (type, id) => {
        selection.select(type, id);
    });
});

function restorePreviousStructure() {
    structure.sections = ObjectCloner.deepClone(previousStructure).sections;
    props.container.messenger.send('editor.structure.restored');
}
function useCurrentStructureAsPrevious() {
    previousStructure = ObjectCloner.deepClone(toRaw(structure));
}






/**********
 * Canvas *
 **********/
const canvasOptions = reactive(ObjectCloner.deepClone(props.canvas));
const Canvas = require("shared/Canvas.js").default;
provide('canvas', new Canvas(
    props.container.messenger,
    canvasOptions.size.breakpoints,
    canvasOptions.size.breakpoint
));





/***********
 * Columns *
 ***********/
const ColumnSize = require("shared/Structure/ColumnSize.js").default;
provide('columnSize', new ColumnSize(structureManipulator));





/**********
 * Modals *
 **********/
const Modals = require("shared/Modals.js").default;
const modalsData = reactive({
    instances: []
});
const modals = new Modals(modalsData);
provide('modals', modals);





/**********
 * Blocks *
 **********/
const Blocks = require('shared/Structure/Blocks/Blocks.js').default;
const BlocksPicker = require("shared/Structure/Blocks/BlocksPicker.js").default;
const BlockHooks = require("shared/Structure/Blocks/BlockHooks.js").default;

const blockPickerData = reactive({
    columnId: null,
    blocks: props.availableBlocks
});
const blockHooks = new BlockHooks(props.container.messenger);
provide('blocks', new Blocks(blockHooks, props.options.blocks));
provide('blocksPicker', new BlocksPicker(blockPickerData, structureManipulator, modals));

</script>
