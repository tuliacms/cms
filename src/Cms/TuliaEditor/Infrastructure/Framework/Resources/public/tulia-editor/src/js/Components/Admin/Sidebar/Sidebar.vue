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
            <div class="tued-sidebar-selector">
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'selected' }" @click="sidebar = 'selected'"><i class="far fa-dot-circle"></i><span>{{ translator.trans('selected') }}</span></div>
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'structure' }" @click="sidebar = 'structure'"><i class="fas fa-stream"></i><span>{{ translator.trans('structure') }}</span></div>
            </div>
            <div :class="{ 'd-block': sidebar === 'structure', 'd-none': sidebar !== 'structure' }">
                <Structure :structure="structure" @selected="sidebar = 'selected'"></Structure>
            </div>
            <div :class="{ 'd-block': sidebar === 'selected', 'd-none': sidebar !== 'selected' }">
                <Selected :structure="structure"></Selected>
            </div>
        </div>
    </div>
</template>

<script setup>
const Structure = require('components/Admin/Sidebar/Structure.vue').default;
const Selected = require('components/Admin/Sidebar/Selected/Selected.vue').default;
const { defineProps, ref, inject, provide, onMounted } = require('vue');

const props = defineProps(['structure']);
const messenger = inject('messenger');
const translator = inject('translator');

provide('structureDragOptions', {
    structureDragOptions: {
        animation: 200,
        disabled: false,
        ghostClass: 'tued-structure-draggable-ghost'
    }
});

const sidebar = ref('structure');

onMounted(() => {
    messenger.on('structure.selection.selected', (type, id, trigger) => {
        if (trigger !== 'sidebar' && type === 'block') {
            sidebar.value = 'selected';
        }
    });
});
</script>
