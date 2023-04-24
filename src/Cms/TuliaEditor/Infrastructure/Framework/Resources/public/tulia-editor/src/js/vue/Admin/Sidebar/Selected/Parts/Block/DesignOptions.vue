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
                <Select class="tued-control-position--bottom" v-model="storage.margin.bottom.data.value" :label="translator.trans('bottom')" :choices="choices"></Select>
                <Select class="tued-control-position--left" v-model="storage.margin.left.data.value" :label="translator.trans('left')" :choices="choices"></Select>
                <Select class="tued-control-position--right" v-model="storage.margin.right.data.value" :label="translator.trans('right')" :choices="choices"></Select>
                <Select class="tued-control-position--top" v-model="storage.margin.top.data.value" :label="translator.trans('top')" :choices="choices"></Select>
            </div>
        </div>
        <div :class="{'tab-pane fade': 1, 'show active': activeTab === 'padding'}" role="tabpanel" tabindex="0">
            <div class="tued-sizing-whirl">
                <Select class="tued-control-position--bottom" v-model="storage.padding.bottom.data.value" :label="translator.trans('bottom')" :choices="choices"></Select>
                <Select class="tued-control-position--left" v-model="storage.padding.left.data.value" :label="translator.trans('left')" :choices="choices"></Select>
                <Select class="tued-control-position--right" v-model="storage.padding.right.data.value" :label="translator.trans('right')" :choices="choices"></Select>
                <Select class="tued-control-position--top" v-model="storage.padding.top.data.value" :label="translator.trans('top')" :choices="choices"></Select>
            </div>
        </div>
    </div>
</template>
<script setup>
import { defineProps, inject, ref, reactive, onMounted } from "vue";

const props = defineProps(['block']);
const options = inject('options');
const translator = inject('translator');
const controls = inject('controls.registry');
const Select = controls.manager('Select');
const storageFactory = inject('breakpointsAwareDataStorageFactory');

const storage = {
    margin: {
        left: storageFactory.ref(props.block.config.margin.left),
        top: storageFactory.ref(props.block.config.margin.top),
        right: storageFactory.ref(props.block.config.margin.right),
        bottom: storageFactory.ref(props.block.config.margin.bottom),
    },
    padding: {
        left: storageFactory.ref(props.block.config.padding.left),
        top: storageFactory.ref(props.block.config.padding.top),
        right: storageFactory.ref(props.block.config.padding.right),
        bottom: storageFactory.ref(props.block.config.padding.bottom),
    },
};

const activeTab = ref('margin');
const choices = reactive({
    '': translator.trans('inheritValue'),
});

const createChoices = () => {
    for (let i = 0; i <= options.elements.style.spacers.max; i++) {
        choices[i] = i;
    }
};

onMounted(() => createChoices());
</script>
