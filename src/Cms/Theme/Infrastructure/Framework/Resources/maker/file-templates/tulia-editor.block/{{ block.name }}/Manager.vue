<template>
    <div class="text-muted text-uppercase">
        {{ translator.trans('noEditOptionsForThisElement') }}
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');

// Your block logic here...
</script>
