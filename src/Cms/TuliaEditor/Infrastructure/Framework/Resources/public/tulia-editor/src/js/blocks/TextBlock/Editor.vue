<template>
    <div>
        <WysiwygEditor v-model="block.data.text"></WysiwygEditor>
    </div>
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('instance.blocks').editor(props);
const extensions = inject('extensions.registry');

const WysiwygEditor = extensions.editor('WysiwygEditor');
</script>
<script>
export default { name: 'Block.Text.Editor' }
</script>
