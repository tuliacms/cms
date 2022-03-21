<template>
    <div class="tued-container" ref="root">
        <Canvas :structure="structure.working"></Canvas>
        <Sidebar :availableBlocks="availableBlocks"></Sidebar>
        <RenderingCanvas ref="rendered-content" :structure="structure.working"></RenderingCanvas>
    </div>
</template>

<script>
import Canvas from './Components/Canvas/Canvas.vue';
import Sidebar from './Components/Sidebar/Sidebar.vue';
import RenderingCanvas from './Components/Rendering/Canvas.vue';
import ObjectCloner from './../../shared/Utils/ObjectCloner.js';

export default {
    props: ['options', 'instanceId', 'messanger', 'availableBlocks'],
    data() {
        return {
            structure: {
                working: {},
                previous: {}
            }
        }
    },
    components: {
        Canvas, Sidebar,
        RenderingCanvas
    },
    methods: {
        save: function () {
            this.useCurrentStructureAsPrevious();
            this.messanger.send(
                'editor.save',
                this.structure.working,
                this.$refs['rendered-content'].$el.innerHTML
            );
        },
        cancel: function () {
            this.restorePreviousStructure();
            this.messanger.send('editor.cancel');
        },
        restorePreviousStructure: function () {
            this.structure.working = ObjectCloner.deepClone(this.structure.previous);
        },
        useCurrentStructureAsPrevious: function () {
            this.structure.previous = ObjectCloner.deepClone(this.structure.working);
        }
    },
    mounted() {
        this.structure.working = ObjectCloner.deepClone(this.options.structure.source);
        this.structure.previous = ObjectCloner.deepClone(this.options.structure.source);

        this.$root.$on('editor.cancel', () => {
            this.cancel();
        });
        this.$root.$on('editor.save', () => {
            this.save();
        });
    }
};
</script>
