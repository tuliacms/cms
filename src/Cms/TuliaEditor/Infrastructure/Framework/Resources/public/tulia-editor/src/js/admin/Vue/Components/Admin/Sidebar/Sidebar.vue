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
                <button type="button" class="tued-main-btn tued-btn-default" @click="$root.$emit('editor.cancel')">Porzuć</button>
                <button type="button" class="tued-main-btn tued-btn-success" @click="$root.$emit('editor.save')">Zapisz</button>
            </div>
            <Structure :structure="structure" :messenger="messenger"></Structure>
        </div>
    </div>
</template>

<script>
import Structure from './Structure.vue';

export default {
    props: ['availableBlocks', 'messenger', 'structure'],
    components: {
        Structure
    }
};
</script>
