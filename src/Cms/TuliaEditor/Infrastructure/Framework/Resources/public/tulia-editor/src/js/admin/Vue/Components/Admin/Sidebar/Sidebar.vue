<template>
    <div class="tued-sidebar">
        <div class="tued-sidebar-inner">
            <div class="tued-controls-group">
                <button type="button" class="tued-btn" @click="$root.$emit('editor.save')">Zapisz</button>
                <button type="button" class="tued-btn" @click="$root.$emit('editor.cancel')">Anuluj</button>
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
