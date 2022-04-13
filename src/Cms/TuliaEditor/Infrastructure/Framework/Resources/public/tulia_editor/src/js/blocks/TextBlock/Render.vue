<template>
    <div v-html="data.text"></div>
</template>

<script>
const props = require('./props.js').default;

export default {
    props: props
}
</script>
