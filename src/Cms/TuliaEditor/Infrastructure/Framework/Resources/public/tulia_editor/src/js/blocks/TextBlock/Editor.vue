<template>
    <div><WysiwygEditor v-model="data.text"></WysiwygEditor></div>
</template>

<script>
const WysiwygEditor = require('extensions/WysiwygEditor.vue').default;
const props = require('./props.js').default;

export default {
    props: props,
    components: { WysiwygEditor },
}
</script>
