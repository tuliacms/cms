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
                <button type="button" class="tued-main-btn tued-btn-default" @click="eventDispatcher.emit('editor.cancel')">{{ translator.trans('cancel') }}</button>
                <button type="button" class="tued-main-btn tued-btn-success" @click="eventDispatcher.emit('editor.save')">{{ translator.trans('save') }}</button>
            </div>
            <div class="tued-sidebar-selector">
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'selected' }" @click="sidebar = 'selected'"><i class="far fa-dot-circle"></i><span>{{ translator.trans('selected') }}</span></div>
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'structure' }" @click="sidebar = 'structure'"><i class="fas fa-stream"></i><span>{{ translator.trans('structure') }}</span></div>
            </div>
            <div v-if="sidebar === 'structure'">
                <Structure :structure="structure"></Structure>
            </div>
            <div v-else-if="sidebar === 'selected'">
                Selected
            </div>
        </div>
    </div>
</template>

<script>
const Structure = require('components/Admin/Sidebar/Structure.vue').default;

export default {
    props: ['structure'],
    inject: ['eventDispatcher', 'translator'],
    components: {
        Structure
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
            sidebar: 'structure',
        };
    }
};
</script>
