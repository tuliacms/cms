<template>
    <div>
        <Select
            v-model="section.config.containerWidth"
            :choices="containerWidthList"
            :label="translator.trans('containerSize')"
        ></Select>
        <Input
            v-model="section.config.anchorId"
            :label="translator.trans('anchorId')"
            :help="translator.trans('anchorIdHelp')"
        ></Input>
    </div>
</template>
<script setup>
import Input from "controls/Input.Text.vue";
import Select from "controls/Select.vue";
import { inject, defineProps } from "vue";

const props = defineProps(['section']);
const section = inject('structure').section(props.section);
const translator = inject('translator');

const containerWidthList = {
    'default': translator.trans('containerSizeDefaultWidth'),
    'full-width': translator.trans('containerSizeFullWidth'),
    'full-width-no-padding': translator.trans('containerSizeFullWidthNoPadding'),
};
</script>
