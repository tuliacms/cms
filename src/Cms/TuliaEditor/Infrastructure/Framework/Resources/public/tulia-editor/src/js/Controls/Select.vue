<template>
    <div class="mb-3">
        <label v-if="props.label" class="form-label">{{ props.label }}</label>
        <div class="tued-control-select tued-closed" ref="control">
            <div class="tued-label" @click="toggleOptions">
                <span v-if="selected">{{ selected }}</span>
                <span v-else class="text-muted">{{ translator.trans('selectSomeOptions') }}</span>
            </div>
            <div class="tued-options">
                <div class="tued-options-group" v-for="(group, index) in choices.list" :key="index">
                    <span v-if="group.label" class="tued-options-group-label">{{ group.label }}</span>
                    <div class="tued-option" v-for="(choice, key) in group.choices" :key="key">
                        <input type="radio" :value="choice.value" v-model="model" :id="`tued-select-${controlId}-${choice.value}`" />
                        <label :for="`tued-select-${controlId}-${choice.value}`" @click="closeOptions">
                            {{ choice.label }}
                        </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-text" v-if="help">{{ help }}</div>
    </div>
</template>

<script setup>
const { defineProps, inject, defineEmits, ref, watch, computed, onMounted, onUnmounted, reactive, toRaw } = require('vue');
const _ = require('lodash');
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    multiple: { require: false, default: false },
    choices: { require: true },
    modelValue: { require: true },
    label: { require: true },
    help: { require: false },
    usesGroups: { require: false, default: false },
});
const messenger = inject('messenger');
const translator = inject('translator');

const choices = reactive({list: {}});
const controlId = ref(null);
const control = ref(null);
const toggleOptions = () => {
    control.value.classList.toggle('tued-opened');
    control.value.classList.toggle('tued-closed');
};
const closeOptions = () => {
    control.value.classList.remove('tued-opened');
    control.value.classList.add('tued-closed');
};
const selected = computed(() => {
    let selected = [];

    for (let g in choices.list) {
        for (let c in choices.list[g].choices) {
            if (choices.list[g].choices[c].value === model.value) {
                selected.push(choices.list[g].choices[c].label);
            }
        }
    }

    return selected.join(', ');
});
const model = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value)
});
const deletectIfCanCloseOptions = function (e) {
    if (control.value.classList.contains('tued-opened') && !control.value.contains(e.target)) {
        closeOptions();
    }
};
const rebuildChoicesWithoutGroups = () => {
    if (!props.usesGroups) {
        let result = [];

        for (let i in props.choices) {
            result.push({ value: i, label: props.choices[i] });
        }

        choices.list = [{ value: null, label: null, choices: result }];
    } else {
        choices.list = props.choices;
    }
};

watch(
    () => props.choices,
    () => rebuildChoicesWithoutGroups(),
    { deep: true }
);

onMounted(() => {
    controlId.value = _.uniqueId();

    rebuildChoicesWithoutGroups();

    document.body.addEventListener('click', deletectIfCanCloseOptions);
    messenger.on('structure.selection.selected', closeOptions);
});
onUnmounted(() => {
    document.body.removeEventListener('click', deletectIfCanCloseOptions);
    messenger.off('structure.selection.selected', closeOptions);
});
</script>
