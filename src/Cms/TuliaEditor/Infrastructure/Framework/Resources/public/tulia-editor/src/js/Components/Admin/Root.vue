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
        <div v-for="(ext, key) in mountedExtensions" :key="key">
            <component :is="ext.code + 'Manager'" :instance="ext.instance"></component>
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
const Instantiator = require("shared/Extension/Instance/Instantiator.js").default;
const extensionRegistry = new ExtensionRegistry(TuliaEditor.extensions);
provide('extension.registry', extensionRegistry);
provide('extension.instance', new Instantiator(props.container.messenger));

const mountedExtensions = reactive([]);

props.container.messenger.operation('extention.mount', (data, success, fail) => {
    mountedExtensions.push({
        instance: data.instance,
        code: data.code
    });

    success();
});

props.container.messenger.operation('extention.unmount', (data, success, fail) => {
    let index = null;

    for (let i in mountedExtensions) {
        if (mountedExtensions[i].instance === data.instance) {
            index = i;
            break;
        }
    }

    mountedExtensions.splice(index, 1);

    success();
});




/*********************
 * Elements instances
 ********************/
const ElementsInstantiator = require('shared/Structure/Element/Instantiator.js').default;
const instantiator = new ElementsInstantiator(props.options, props.container.messenger, extensionRegistry, structureManipulator);

provide('blocks.instance', instantiator.instantiator('block'));
provide('columns.instance', instantiator.instantiator('column'));
provide('rows.instance', instantiator.instantiator('row'));
provide('sections.instance', instantiator.instantiator('section'));



/*********
 * Blocks
 *********/
const BlocksPicker = require('shared/Structure/Blocks/BlocksPicker.js').default;
const BlocksRegistry = require('shared/Structure/Blocks/Registry.js').default;

const blockPickerData = reactive({
    columnId: null
});
const blocksRegistry = new BlocksRegistry(props.availableBlocks);

provide('blocks.registry', blocksRegistry);
provide('blocks.picker', new BlocksPicker(blockPickerData, blocksRegistry, structureManipulator, modals));


/**********
 * Columns
 **********/
const ColumnSize = require("shared/Structure/Columns/ColumnSize.js").default;
provide('columns.size', new ColumnSize(structureManipulator));
</script>
