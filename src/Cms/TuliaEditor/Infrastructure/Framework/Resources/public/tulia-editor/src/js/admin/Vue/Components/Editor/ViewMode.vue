<template>
    <div class="tued-view-mode-selector">
        <div>Tryb:</div>
        <div
            v-for="(mode, key) in modes"
            :class="{ 'tued-view-mode-btn': true, 'tued-view-mode-btn-active': viewMode === key }"
            @click="change(key)"
        >
            {{ mode.label }}
        </div>
    </div>
</template>

<script>
export default {
    props: ['viewMode'],
    data () {
        return {
            modes: {
                edit: { label: 'Edycji' },
                structure: { label: 'Struktury' },
                preview: { label: 'Podglądu' },
            }
        }
    },
    methods: {
        change (mode) {
            this.viewMode = mode;
            this.$emit('changed', mode);
        }
    }
};
</script>
