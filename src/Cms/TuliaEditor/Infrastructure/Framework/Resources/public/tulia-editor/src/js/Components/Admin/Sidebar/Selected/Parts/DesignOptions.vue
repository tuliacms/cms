<template>
    <ul class="nav nav-tabs nav-justified mb-0">
        <li class="nav-item">
            <a :class="{'nav-link': 1, 'active': activeTab === 'margin'}" href="#" @click.stop="activeTab = 'margin'">{{ translator.trans('margin') }}</a>
        </li>
        <li class="nav-item">
            <a :class="{'nav-link': 1, 'active': activeTab === 'padding'}" href="#" @click.stop="activeTab = 'padding'">{{ translator.trans('padding') }}</a>
        </li>
    </ul>
    <div class="tab-content px-2 py-3 mb-3" style="border:1px solid #dee2e6;border-top:none;">
        <div :class="{'tab-pane fade': 1, 'show active': activeTab === 'margin'}" role="tabpanel" tabindex="0">
            <div class="tued-sizing-whirl">
                <Select class="tued-control-position--bottom" v-model="storage.data.margin.bottom" :label="translator.trans('bottom')" :choices="choices"></Select>
                <Select class="tued-control-position--left" v-model="storage.data.margin.left" :label="translator.trans('left')" :choices="choices"></Select>
                <Select class="tued-control-position--right" v-model="storage.data.margin.right" :label="translator.trans('right')" :choices="choices"></Select>
                <Select class="tued-control-position--top" v-model="storage.data.margin.top" :label="translator.trans('top')" :choices="choices"></Select>
            </div>
        </div>
        <div :class="{'tab-pane fade': 1, 'show active': activeTab === 'padding'}" role="tabpanel" tabindex="0">
            <div class="tued-sizing-whirl">
                <Select class="tued-control-position--bottom" v-model="storage.data.padding.bottom" :label="translator.trans('bottom')" :choices="choices"></Select>
                <Select class="tued-control-position--left" v-model="storage.data.padding.left" :label="translator.trans('left')" :choices="choices"></Select>
                <Select class="tued-control-position--right" v-model="storage.data.padding.right" :label="translator.trans('right')" :choices="choices"></Select>
                <Select class="tued-control-position--top" v-model="storage.data.padding.top" :label="translator.trans('top')" :choices="choices"></Select>
            </div>
        </div>
    </div>
</template>
<script setup>
const BreakpointsAwareDataStorage = require('shared/Structure/Element/Data/BreakpointsAwareDataStorage.js').default;
const { defineProps, inject, ref, reactive, onMounted } = require('vue');
const props = defineProps(['block']);
const options = inject('options');
const translator = inject('translator');
const block = inject('blocks.instance').manager(props);
const Select = block.control('Select');

const storage = BreakpointsAwareDataStorage.reactive(block, '_internal.sizing', {
    margin: {top: undefined, right: undefined, bottom: undefined, left: undefined},
    padding: {top: undefined, right: undefined, bottom: undefined, left: undefined},
}, '');

const activeTab = ref('margin');
const choices = reactive({
    '': translator.trans('inheritValue'),
});

const createChoices = () => {
    for (let i = 0; i <= options.elements.style.spacers.max; i++) {
        choices[i] = i;
    }
};

onMounted(() => {
    createChoices()
});
</script>
