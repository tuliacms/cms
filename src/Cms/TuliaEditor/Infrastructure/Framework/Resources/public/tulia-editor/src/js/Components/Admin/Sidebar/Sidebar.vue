<template>
    <div class="tued-sidebar">
        <div class="tued-sidebar-inner">
            <div class="tued-sidebar-toolbar">
                <div class="tued-menu-holder">
                    <div class="tued-menu-hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>
                <button type="button" class="tued-main-btn tued-main-btn-default" @click="$emit('cancel')">{{ translator.trans('cancel') }}</button>
                <button type="button" class="tued-main-btn tued-main-btn-success" @click="$emit('save')">{{ translator.trans('save') }}</button>
            </div>
            <div class="tued-sidebar-selector">
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'selected' }" @click="sidebar = 'selected'"><i class="far fa-dot-circle"></i><span>{{ translator.trans('selected') }}</span></div>
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'structure' }" @click="sidebar = 'structure'"><i class="fas fa-stream"></i><span>{{ translator.trans('structure') }}</span></div>
            </div>
            <div :class="{ 'd-block': sidebar === 'structure', 'd-none': sidebar !== 'structure' }">
                <Structure :structure="structure"></Structure>
            </div>
            <div :class="{ 'd-block': sidebar === 'selected', 'd-none': sidebar !== 'selected' }">
                <Selected :structure="structure"></Selected>
            </div>
        </div>
    </div>
</template>

<script>
const Structure = require('components/Admin/Sidebar/Structure.vue').default;
const Selected = require('components/Admin/Sidebar/Selected/Selected.vue').default;

export default {
    props: ['structure'],
    inject: ['messenger', 'translator'],
    components: {
        Structure,
        Selected
    },
    provide () {
        return {
            structureDragOptions: {
                animation: 200,
                disabled: false,
                ghostClass: 'tued-structure-draggable-ghost'
            }
        };
    },
    data () {
        return {
            sidebar: 'structure'
        };
    },
    mounted () {
        this.messenger.on('structure.selection.selected', (id, type, trigger) => {
            if (trigger !== 'sidebar') {
                this.sidebar = 'selected';
            }
        });
    }
};
</script>
