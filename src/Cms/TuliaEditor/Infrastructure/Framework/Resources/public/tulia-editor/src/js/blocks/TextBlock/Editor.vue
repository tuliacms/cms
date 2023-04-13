<template>
    <div>
        Config: {{ block.config.someConfig }}
        <!--<WysiwygEditor v-model="block.data.text"></WysiwygEditor>-->
    </div>
</template>

<script setup>
import { defineProps, inject } from "vue";
const props = defineProps(['block']);
const block = inject('instance.blocks').editor(props);

/*const WysiwygEditor = block.extension('WysiwygEditor');*/
</script>
