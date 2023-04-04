<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <Sidebar
                @contextmenu="(event) => contextmenu.open(event)"
            ></Sidebar>
            <Canvas
                :editorView="options.editor.view + '?tuliaEditorInstance=' + instanceId"
                ref="canvas"
            ></Canvas>
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
import { provide, defineProps, onMounted, ref } from "vue";

const props = defineProps(['container']);

provide('structureDragOptions', {
    structureDragOptions: {
        animation: 200,
        disabled: false,
        ghostClass: 'tued-structure-draggable-ghost'
    }
});

const options = props.container.getParameter('options');
const instanceId = props.container.getParameter('instanceId');


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
    document.body.addEventListener('click', (e) => contextmenu.hide());
});








/**
 * Every service from container must be provided at the end, because we produce thos services
 * here, and if any of then has dependency to any services created in this file, then this will fail.
 */
provide('container', props.container);
provide('translator', props.container.get('translator'));
provide('options', props.container.getParameter('options'));
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
</script>
<script>
export default {
    name: 'Tulia Editor - Admin',
}
</script>
