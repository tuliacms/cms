<template>
    <div class="tued-sidebar">
        <div class="tued-sidebar-inner">
            <div class="tued-sidebar-toolbar">
                <!--<div class="dropdown">
                    <div class="tued-menu-holder" data-bs-toggle="dropdown">
                        <div class="tued-menu-hamburger">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item dropdown-item-with-icon" href="#" @click="messenger.execute('editor.canvas.preview.toggle')"><i class="dropdown-icon fa fa-eye"></i> Podgląd</a></li>
                    </ul>
                </div>-->
                <button type="button" class="tued-main-btn tued-main-btn-default" @click="$emit('cancel')">{{ translator.trans('cancel') }}</button>
                <button type="button" class="tued-main-btn tued-main-btn-success" @click="$emit('save')">{{ translator.trans('save') }}</button>
            </div>
            <div :class="{ 'tued-sidebar-selector': true, 'tued-sidebar-selector-has-debug': options.debug }" @click.stop="" @mouseup.stop="" @mousedown.stop="">
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar.tab === 'selected' }" @click="openTab('selected')"><i class="far fa-dot-circle"></i><span>{{ translator.trans('selected') }}</span></div>
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar.tab === 'structure' }" @click="openTab('structure')"><i class="fas fa-stream"></i><span>{{ translator.trans('structure') }}</span></div>
                <div v-if="options.debug" :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar.tab === 'debug' }" @click="openTab('debug')"><i class="fas fa-code"></i><span>Debug</span></div>
            </div>
            <div :class="{ 'd-block': sidebar.tab === 'structure', 'd-none': sidebar.tab !== 'structure' }">
                <Structure @selected="sidebar.tab = 'selected'"></Structure>
            </div>
            <div :class="{ 'd-block': sidebar.tab === 'selected', 'd-none': sidebar.tab !== 'selected' }" @click.stop="" @mouseup.stop="" @mousedown.stop="">
                <Selected></Selected>
            </div>
            <div v-if="options.debug" :class="{ 'd-block': sidebar.tab === 'debug', 'd-none': sidebar.tab !== 'debug' }" @click.stop="" @mouseup.stop="" @mousedown.stop="">
<!--                <Debug @disableSwitchTabs="sidebar.disabled = true" @enableSwitchTabs="sidebar.disabled = false"></Debug>-->
            </div>
        </div>
    </div>
</template>

<script setup>
import Structure from "admin/Sidebar/Structure.vue";
import Selected from "admin/Sidebar/Selected/Selected.vue";
/*const Debug = require('components/Admin/Debug/Debug.vue').default;*/
import { defineProps, ref, inject, provide, onMounted, reactive } from "vue";

/*const props = defineProps(['structure']);
const messenger = inject('messenger');*/
const options = inject('options');
const translator = inject('translator');
const admin = inject('admin');

provide('structureDragOptions', {
    structureDragOptions: {
        animation: 200,
        disabled: false,
        ghostClass: 'tued-structure-draggable-ghost'
    }
});

const sidebar = reactive({
    tab: 'structure',
    disabled: false,
});

const openTab = (tab) => {
    if (!sidebar.disabled) {
        sidebar.tab = tab;
    }
};

/*onMounted(() => {
    messenger.on('structure.selection.selected', (type, id, trigger) => {
        if (trigger !== 'sidebar' && type === 'block') {
            openTab('selected');
        }
    });
});*/
</script>
