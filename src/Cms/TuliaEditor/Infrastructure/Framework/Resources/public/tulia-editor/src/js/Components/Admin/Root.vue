<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <CanvasComponent
                :editorView="options.editor.view + '?tuliaEditorInstance=' + instanceId"
                :canvasOptions="canvasOptions"
            ></CanvasComponent>
            <SidebarComponent
                :structure="structure"
                @cancel="cancelEditor"
                @save="saveEditor"
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
const TuliaEditor = require('TuliaEditor');
const { defineProps, onMounted, provide, reactive, isProxy, toRaw } = require('vue');

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
provide('options', props.options);

const saveEditor = function () {
    props.container.messenger.execute('structure.fetch').then((data) => {
        selection.resetHovered();
        selection.resetSelection();
        structure.sections = data.structure.sections;
        useCurrentStructureAsPrevious();
        props.editor.updateContent(data.structure, data.content, data.style);
        props.editor.closeEditor();
        props.container.messenger.notify('editor.save');
    });
};

const cancelEditor = function () {
    selection.resetHovered();
    selection.resetSelection();
    restorePreviousStructure();
    props.container.messenger.notify('structure.synchronize.from.admin', ObjectCloner.deepClone(toRaw(structure)));
    props.editor.closeEditor();
    props.container.messenger.notify('editor.cancel');
};




/************
 * Structure
 ************/
const StructureManipulator = require('shared/Structure/StructureManipulator.js').default;
const Selection = require("shared/Structure/Selection/Selection.js").default;

// 'structure' store structure live updated from Editor iframe instance.
// Default value of this field is a value from 'options' passed in new instance creation.
const structure = reactive(props.structure);

if (!structure.sections) {
    structure.sections = [];
}

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
        selection.select(type, id, 'editor');
    });
});

function restorePreviousStructure() {
    structure.sections = ObjectCloner.deepClone(previousStructure).sections;
    props.container.messenger.notify('editor.structure.restored');
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





/**********
 * Columns
 **********/
const ColumnSize = require("shared/Structure/Columns/ColumnSize.js").default;
provide('columnSize', new ColumnSize(structureManipulator));





/*********
 * Modals
 *********/
const Modals = require("shared/Modals.js").default;
const modalsData = reactive({
    instances: []
});
const modals = new Modals(modalsData);
provide('modals', modals);



/*************
 * Extensions
 ************/
const ExtensionRegistry = require("shared/Extension/Registry.js").default;
const extensionRegistry = new ExtensionRegistry(TuliaEditor.extensions);
provide('extensionRegistry', extensionRegistry);



/*********
 * Blocks
 *********/
const Blocks = require('shared/Structure/Blocks/Blocks.js').default;
const BlocksPicker = require('shared/Structure/Blocks/BlocksPicker.js').default;
const BlocksRegistry = require('shared/Structure/Blocks/Registry.js').default;

const blockPickerData = reactive({
    columnId: null
});
const blocksRegistry = new BlocksRegistry(props.availableBlocks);

provide('blocksRegistry', blocksRegistry);
provide('blocks', new Blocks(props.options.blocks, props.container.messenger, extensionRegistry));
provide('blocksPicker', new BlocksPicker(blockPickerData, blocksRegistry, structureManipulator, modals));

/**********
 * Columns
 **********/
const Columns = require('shared/Structure/Columns/Columns.js').default;
provide('columns', new Columns(props.options.columns, props.container.messenger, extensionRegistry));

/**********
 * Rows
 **********/
const Rows = require('shared/Structure/Rows/Rows.js').default;
provide('rows', new Rows(props.options.rows, props.container.messenger, extensionRegistry));

/**********
 * Sections
 **********/
const Sections = require('shared/Structure/Sections/Sections.js').default;
provide('sections', new Sections(props.options.sections, props.container.messenger, extensionRegistry));
</script>
