<template>
    <div
        class="tued-preview"
        v-bind:class="{ 'tued-hidden': viewMode === 'editor' }"
    >
        <div class="tued-preview-toolbar">
            <button type="button" class="tued-btn" v-on:click="$emit('edit')">Edit</button>
        </div>
        <iframe class="tued-preview-iframe"></iframe>
    </div>
</template>

<script>
export default {
    props: {
        viewMode: { type: String },
    }
};
</script>
