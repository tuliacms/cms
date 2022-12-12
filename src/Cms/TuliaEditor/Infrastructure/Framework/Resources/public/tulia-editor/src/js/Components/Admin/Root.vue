<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <CanvasComponent
                :editorView="options.editor.view + '?tuliaEditorInstance=' + instanceId"
                :canvasOptions="canvasOptions"
                ref="canvas"
            ></CanvasComponent>
            <SidebarComponent
                :structure="structure"
                @cancel="cancelEditor"
                @save="saveEditor"
                @contextmenu="(event) => cx.open(event, 'sidebar')"
            ></SidebarComponent>
            <Debugbar></Debugbar>
            <BlockPickerComponent
                :availableBlocks="availableBlocks"
                :blockPickerData="blockPickerData"
            ></BlockPickerComponent>
        </div>
        <div v-for="(ext, key) in mountedExtensions" :key="key">
            <component :is="ext.code + 'Manager'" :instance="ext.instance"></component>
        </div>
        <Teleport to="body">
            <div class="dropdown-menu show tued-contextmenu" v-if="contextmenu.opened" :style="{ top: contextmenu.position.y + 'px', left: contextmenu.position.x + 'px' }">
                <div v-for="group in contextmenu.items.collection" class="tued-dropdown-menu-group">
                    <div v-if="group.label"><h6 class="dropdown-header">{{ group.label }}</h6></div>
                    <div v-for="item in group.items">
                        <a :class="contextmenuItemClass(item)" href="#" @click="item.callback()">
                            <i v-if="item.icon" :class="contextmenuItemIcon(item)"></i>
                            {{ item.label }}
                        </a>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
const CanvasComponent = require("components/Admin/Canvas/Canvas.vue").default;
const SidebarComponent = require("components/Admin/Sidebar/Sidebar.vue").default;
const BlockPickerComponent = require("components/Admin/Block/PickerModal.vue").default;
const Debugbar = require("components/Admin/Debugbar/Debugbar.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const TuliaEditor = require('TuliaEditor');
const { defineProps, onMounted, provide, reactive, isProxy, toRaw, ref } = require('vue');

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
        data.structure.assets = assets.collect();

        if (data.structure.assets.length) {
            data.content += `[assets names="${data.structure.assets.join(',')}"]`;
        }

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




/*********
 * Canvas
 *********/
const canvasOptions = reactive(ObjectCloner.deepClone(props.canvas));
const CanvasView = require("shared/Canvas/View.js").default;
const Canvas = require("shared/Canvas.js").default;
provide('canvas', new Canvas(
    props.container.messenger,
    canvasOptions.size.breakpoints,
    canvasOptions.size.breakpoint
));
const canvasView = new CanvasView(props.container.messenger);
provide('canvas.view', canvasView);




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
        canvasView.update();
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



/***************
 * Contextmenu
 **************/
const Contextmenu = require("shared/Contextmenu/Contextmenu.js").default;
const cx = new Contextmenu('sidebar', props.container.messenger);
provide('contextmenu', cx);

const canvas = ref(null);
const contextmenu = reactive({
    items: [],
    opened: false,
    position: {
        x: 0,
        y: 0
    }
});
const openContextmenu = (event) => {
    if (!event) {
        return;
    }

    if (event.source === 'editor') {
        const iframe = canvas.value.$el.querySelector('.tued-canvas-device-faker iframe');
        event.position.x = event.position.x + iframe.getBoundingClientRect().left;
        event.position.y = event.position.y + iframe.getBoundingClientRect().top;
    }

    contextmenu.items = cx.collectItems(event.targets);

    contextmenu.position.x = event.position.x;
    contextmenu.position.y = event.position.y;
    contextmenu.opened = contextmenu.items.total >= 1;
};

const hideContextmenu = () => {
    contextmenu.opened = false;
};

const contextmenuItemIcon = item => {
    return 'dropdown-icon ' + item.icon;
};
const contextmenuItemClass = item => {
    let classname = 'dropdown-item';

    if (item.icon) {
        classname += ' dropdown-item-with-icon';
    }

    return classname + ' ' + item.classname;
};



/***********
 * Controls
 **********/
const ControlRegistry = require("shared/Control/Registry.js").default;
const controlRegistry = new ControlRegistry(TuliaEditor.controls);
provide('control.registry', controlRegistry);



/*********
 * Assets
 *********/
const Assets = require("shared/Assets/Assets.js").default;
const assets = new Assets();
provide('assets', assets);



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
    cx,
    assets,
);

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




onMounted(() => {
    props.container.messenger.operation('contextmenu', (data, success, fail) => {
        openContextmenu(data);
        success();
    });
    props.container.messenger.operation('contextmenu.hide', (data, success, fail) => {
        hideContextmenu();
        success();
    });

    document.body.addEventListener('click', (e) => {
        props.container.messenger.execute('contextmenu.hide');
    });
});
</script>
<script>
export default {
    name: 'Tulia Editor'
}
</script>
