<template>
    <div class="tulia-editor-container" ref="root">
        <Preview
            v-bind:viewMode="options.layout.viewMode"
            v-on:edit="edit"
        ></Preview>
        asdasd
    </div>
</template>

<script>
import Preview from "./Components/Preview.vue";

export default {
    components: { Preview },
    data () {
        return {
            options: {
                layout: {
                    /**
                     * View mode of the whole editor. Available options:
                     * 'preview' - Shows the preview of the content
                     * 'editor' - Shows the editor with defined content
                     */
                    viewMode: 'editor',
                    /**
                     * Edit mode type. Available options:
                     * 'inline' - Shows the editor inline
                     * 'fullscreen' - Shows editor in fullscreen
                     */
                    editMode: 'fullscreen',
                },
                assets: {
                    styles: Array,
                    scripts: Array
                }
            }
        }
    },
    methods: {
        edit: function () {
            this.options.layout.viewMode = 'editor';
        },
        save: function () {
            this.options.layout.viewMode = 'preview';
        },
        cancel: function () {
            this.options.layout.viewMode = 'preview';
        }
    }
};
</script>

<style lang="scss">
    @import './../../../css/tulia-editor.admin.scss';
</style>
