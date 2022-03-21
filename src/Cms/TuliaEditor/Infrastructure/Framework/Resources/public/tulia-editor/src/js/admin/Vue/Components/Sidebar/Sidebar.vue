<template>
    <div class="tued-sidebar">
        <div class="tued-sidebar-inner">
            <div class="tued-controls-group">
                <button type="button" class="tued-btn" @click="$root.$emit('editor.save')">Zapisz</button>
                <button type="button" class="tued-btn" @click="$root.$emit('editor.cancel')">Anuluj</button>
            </div>
            Edit controls
        </div>
    </div>
</template>

<script>
export default {
    props: ['availableBlocks'],
};
</script>
