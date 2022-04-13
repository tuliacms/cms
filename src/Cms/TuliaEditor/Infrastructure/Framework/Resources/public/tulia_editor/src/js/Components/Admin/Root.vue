<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <Canvas
                :editorView="options.editor.view + '?tuliaEditorInstance=' + instanceId"
                :canvas="canvas"
            ></Canvas>
            <Sidebar
                :availableBlocks="availableBlocks"
                :structure="structure"
                :canvas="canvas"
            ></Sidebar>
        </div>
    </div>
</template>

<script setup>
const Canvas = require("components/Admin/Canvas/Canvas.vue").default;
const Sidebar = require("components/Admin/Sidebar/Sidebar.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const Selection = require("shared/Structure/Selection/Selection.js").default;
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

// 'structure' store structure live updated from Editor iframe instance.
// Default value of this field is a value from 'options' passed in new instance creation.
const structure = reactive(ObjectCloner.deepClone(props.structure));
let previousStructure = ObjectCloner.deepClone(props.structure);

const selection = new Selection(structure, props.container.messenger);

provide('messenger', props.container.messenger);
provide('translator', props.container.translator);
provide('eventDispatcher', props.container.eventDispatcher);
provide('selection', selection);

onMounted(() => {
    props.container.eventDispatcher.on('editor.save', () => {
        props.container.messenger.listen('structure.rendered.data', (content, newStructure) => {
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
    props.container.eventDispatcher.on('device.size.changed', (size) => {
        props.container.messenger.send('device.size.changed', size);
    });
    props.container.eventDispatcher.on('structure.column.resize', (columnsId, size) => {
        props.container.messenger.send('structure.synchronize.from.admin', ObjectCloner.deepClone(toRaw(structure)));
    });
    props.container.messenger.listen('structure.updated', () => {
        selection.update();
    });



    props.container.messenger.listen('structure.synchronize.from.editor', (newStructure) => {
        structure.sections = newStructure.sections;
        props.container.messenger.send('structure.updated');
    });
});

function restorePreviousStructure() {
    structure.sections = ObjectCloner.deepClone(previousStructure).sections;
    props.container.messenger.send('editor.structure.restored');
}
function useCurrentStructureAsPrevious() {
    previousStructure = ObjectCloner.deepClone(toRaw(structure));
}
</script>
