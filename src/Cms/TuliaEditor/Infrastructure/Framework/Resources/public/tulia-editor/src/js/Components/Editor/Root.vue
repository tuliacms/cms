<template>
    <div :class="{ 'tued-container': true, 'tued-show-preview': renderPreview }">
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
const StyleCompiler = require('shared/Structure/Element/Style/Compiler.js').default;
const Filemanager = require('shared/Filemanager.js').default;
const TuliaEditor = require('TuliaEditor');
const { defineProps, provide, reactive, onMounted, toRaw, ref } = require('vue');

const props = defineProps([
    'container',
    'instanceId',
    'options',
    'availableBlocks',
    'structure'
]);

const structure = reactive(ObjectCloner.deepClone(props.structure));

if (!structure.sections) {
    structure.sections = [];
}

const selection = new Selection(structure, props.container.messenger);
const structureManipulator = new StructureManipulator(structure, props.container.messenger);

provide('selection', selection);
provide('messenger', props.container.messenger);
provide('translator', props.container.translator);
provide('structureManipulator', structureManipulator);
provide('options', props.options);
provide('filemanager', new Filemanager(props.options.filemanager));

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

    document.addEventListener('click', event => {
        if (event.target.tagName === 'HTML') {
            props.container.messenger.notify('editor.click.outside');
        }
    });

    document.addEventListener('click', event => {
        props.container.messenger.execute('contextmenu.hide');
    });
    document.addEventListener('contextmenu', event => {
        contextmenu.open(event, 'editor');
    });
});





/*************
 * Extensions
 ************/
const ExtensionRegistry = require("shared/Extension/Registry.js").default;
const Instantiator = require("shared/Extension/Instance/Instantiator.js").default;
const extensionRegistry = new ExtensionRegistry(TuliaEditor.extensions);
provide('extension.registry', extensionRegistry);
provide('extension.instance', new Instantiator(props.container.messenger));


/***************
 * Contextmenu
 **************/
const Contextmenu = require("shared/Contextmenu/Contextmenu.js").default;
const contextmenu = new Contextmenu('editor', props.container.messenger);
provide('contextmenu', contextmenu);


/***********
 * Controls
 **********/
const ControlRegistry = require("shared/Control/Registry.js").default;
const controlRegistry = new ControlRegistry(TuliaEditor.controls);
provide('control.registry', controlRegistry);


/*********************
 * Elements instances
 ********************/
const ElementsInstantiator = require('shared/Structure/Element/Instantiator.js').default;
const instantiator = new ElementsInstantiator(
    props.options,
    props.container.messenger,
    extensionRegistry,
    controlRegistry,
    structureManipulator,
    contextmenu,
);

provide('blocks.instance', instantiator.instantiator('block'));
provide('columns.instance', instantiator.instantiator('column'));
provide('rows.instance', instantiator.instantiator('row'));
provide('sections.instance', instantiator.instantiator('section'));


/*********
 * Blocks
 *********/
const BlocksRegistry = require("shared/Structure/Blocks/Registry.js").default;
provide('blocks.registry', new BlocksRegistry(props.availableBlocks));



/**********************
 * Render area preview
 **********************/
const renderPreview = ref(props.options.show_preview_in_canvas);
props.container.messenger.operation('editor.canvas.preview.toggle', (params, success, fail) => {
    renderPreview.value = !renderPreview.value;

    props.container.messenger.notify('editor.canvas.preview.toggled', renderPreview.value);

    success();
});
const CanvasView = require("shared/Canvas/View.js").default;
provide('canvas.view', new CanvasView(props.container.messenger));
</script>

