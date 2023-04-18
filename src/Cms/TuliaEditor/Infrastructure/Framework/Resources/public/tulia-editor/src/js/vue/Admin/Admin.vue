<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <Sidebar
                @contextmenu="(event) => contextmenu.open(event)"
                @save="saveEditor"
                @cancel="cancelEditor"
            ></Sidebar>
            <Canvas ref="canvas"></Canvas>
            <!--<SidebarComponent
                :structure="structure"
                @cancel="cancelEditor"
                @save="saveEditor"
                @contextmenu="(event) => cx.open(event, 'sidebar')"
            ></SidebarComponent>
            <BlockPickerComponent
                :availableBlocks="availableBlocks"
                :blockPickerData="blockPickerData"
            ></BlockPickerComponent>-->
        </div>
        <!--<div v-for="(ext, key) in mountedExtensions" :key="key">
            <component :is="ext.code + 'Manager'" :instance="ext.instance"></component>
        </div>-->
        <Teleport to="body">
            <div class="dropdown-menu show tued-contextmenu" v-if="contextmenuStore.opened" :style="{ top: contextmenuStore.position.y + 'px', left: contextmenuStore.position.x + 'px' }">
                <div v-for="group in contextmenuStore.items" class="tued-dropdown-menu-group">
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
import Sidebar from "admin/Sidebar/Sidebar.vue";
import Canvas from "admin/Canvas/Canvas.vue";
import {provide, defineProps, onMounted, ref, toRaw} from "vue";

const props = defineProps(['container', 'editor']);

provide('structureDragOptions', {
    structureDragOptions: {
        animation: 200,
        disabled: false,
        ghostClass: 'tued-structure-draggable-ghost'
    }
});

/**************
 * Contextmenu
 *************/
const contextmenu = props.container.get('usecase.contextmenu');
const contextmenuStore = props.container.get('contextmenu.store');

const canvas = ref(null);

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

contextmenu.setEditorOffsetProvider(() => {
    const iframe = canvas.value.$el.querySelector('.tued-canvas-device-faker iframe');

    return {
        left: iframe.getBoundingClientRect().left,
        top: iframe.getBoundingClientRect().top,
    };
});

onMounted(() => {
    document.body.addEventListener('click', () => contextmenu.hide());
});


/**
 * Save and Cancel
 */
const cancelEditor = () => {
    //restorePreviousStructure();
    //props.container.messenger.notify('structure.synchronize.from.admin', ObjectCloner.deepClone(toRaw(structure)));
    props.container.get('usecase.editorWindow').cancel();
};
const saveEditor = () => {
    props.container.get('usecase.editorWindow').save();
};






/**
 * Every service from container must be provided at the end, because we produce those services
 * here, and if any of them has dependency to any services created in this file, then this will fail.
 */
provide('container', props.container);
provide('instanceId', props.container.getParameter('instanceId'));
provide('options', props.container.getParameter('options'));
provide('translator', props.container.get('translator'));
provide('eventBus', props.container.get('eventBus'));
provide('admin', props.container.get('admin'));
provide('canvas', props.container.get('canvas'));
provide('usecase.sections', props.container.get('usecase.sections'));
provide('usecase.rows', props.container.get('usecase.rows'));
provide('usecase.columns', props.container.get('usecase.columns'));
provide('usecase.selection', props.container.get('usecase.selection'));
provide('usecase.draggable', props.container.get('usecase.draggable'));
provide('usecase.contextmenu', props.container.get('usecase.contextmenu'));
provide('messenger', props.container.get('messenger'));
provide('structure.store', props.container.get('structure.store'));
provide('selection.store', props.container.get('selection.store'));
provide('instance.blocks', props.container.get('instantiator.block'));
provide('instance.columns', props.container.get('instantiator.column'));
provide('instance.rows', props.container.get('instantiator.row'));
provide('instance.sections', props.container.get('instantiator.section'));
provide('columnSize', props.container.get('columnSize'));
provide('blocks.registry', props.container.get('blocks.registry'));
</script>
<script>
export default {
    name: 'Tulia Editor - Admin',
}
</script>
