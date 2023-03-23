<template>
    <div class="tued-editor-window-inner">
        <div class="tued-container">
            <Sidebar></Sidebar>
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
        </Teleport>-->
    </div>
</template>

<script setup>
import Sidebar from "admin/Sidebar/Sidebar.vue";
import Canvas from "admin/Canvas/Canvas.vue";
import { provide, defineProps } from "vue";

const props = defineProps(['container']);

provide('structureDragOptions', {
    structureDragOptions: {
        animation: 200,
        disabled: false,
        ghostClass: 'tued-structure-draggable-ghost'
    }
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
provide('messenger', props.container.get('messenger'));
provide('structure', props.container.get('structure'));

const options = props.container.getParameter('options');
const instanceId = props.container.getParameter('instanceId');
</script>
<script>
export default {
    name: 'Tulia Editor - Admin',
}
</script>
