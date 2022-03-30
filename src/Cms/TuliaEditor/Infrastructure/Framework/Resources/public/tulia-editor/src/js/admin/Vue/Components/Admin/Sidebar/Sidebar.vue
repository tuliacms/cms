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
                <button type="button" class="tued-main-btn tued-btn-default" @click="$root.$emit('editor.cancel')">{{ translator.trans('cancel') }}</button>
                <button type="button" class="tued-main-btn tued-btn-success" @click="$root.$emit('editor.save')">{{ translator.trans('save') }}</button>
            </div>
            <div class="tued-sidebar-selector">
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'blocks' }" @click="sidebar = 'blocks'"><i class="fas fa-plus-square"></i><span>Add</span></div>
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'selected' }" @click="sidebar = 'selected'"><i class="far fa-dot-circle"></i><span>Selected</span></div>
                <div :class="{ 'tued-sidebar-type': true, 'tued-sidebar-type-active': sidebar === 'structure' }" @click="sidebar = 'structure'"><i class="fas fa-stream"></i><span>Structure</span></div>
            </div>
            <div v-if="sidebar === 'structure'">
                <Structure :structure="structure" :messenger="messenger" :canvas="canvas"></Structure>
            </div>
            <div v-if="sidebar === 'selected'">
                Selected
            </div>
            <div v-if="sidebar === 'blocks'">
                <div class="tued-block-selector">
                    <div
                        class="tued-block-item"
                        v-for="block in blocks"
                        :key="block.code"
                    >
                        <img :src="block.icon" />
                        {{ block.name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Structure from './Structure.vue';

export default {
    props: ['availableBlocks', 'messenger', 'structure', 'canvas', 'translator'],
    components: {
        Structure
    },
    data () {
        let blocks = [];

        for (let i in TuliaEditor.blocks) {
            blocks.push({
                code: TuliaEditor.blocks[i].code,
                name: TuliaEditor.blocks[i].name,
                icon: TuliaEditor.blocks[i].icon,
            });
        }

        return {
            sidebar: 'structure',
            blocks: blocks
        }
    }
};
</script>
