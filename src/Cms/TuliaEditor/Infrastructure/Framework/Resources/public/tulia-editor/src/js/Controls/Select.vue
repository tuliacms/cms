<template>
    <div class="mb-3">
        <label class="form-label">{{ props.label }}</label>
        <div class="tued-control-select tued-closed" ref="control">
            <div class="tued-label" @click="toggleOptions">
                <span v-if="selected">{{ selected }}</span>
                <span v-else class="text-muted">{{ translator.trans('selectSomeOptions') }}</span>
            </div>
            <div class="tued-options">
                <div class="tued-option" v-for="(label, value) in props.choices" :key="value">
                    <input type="radio" :value="value" v-model="model" :id="`tued-select-${controlId}-${value}`" />
                    <label :for="`tued-select-${controlId}-${value}`" @click="closeOptions">
                        {{ label }}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-text" v-if="help">{{ help }}</div>
    </div>
</template>

<script setup>
const { defineProps, inject, defineEmits, ref, watch, computed, onMounted, onUnmounted } = require('vue');
const _ = require('lodash');
const emit = defineEmits(['update:modelValue']);
const props = defineProps({
    multiple: { require: false, default: false },
    choices: { require: true },
    modelValue: { require: true },
    label: { require: true },
    help: { require: false },
});
const messenger = inject('messenger');
const translator = inject('translator');

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

    for (let i in props.choices) {
        if (i === model.value) {
            selected.push(props.choices[i]);
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

onMounted(() => {
    controlId.value = _.uniqueId();

    document.body.addEventListener('click', deletectIfCanCloseOptions);
    messenger.on('structure.selection.selected', closeOptions);
});
onUnmounted(() => {
    document.body.removeEventListener('click', deletectIfCanCloseOptions);
    messenger.off('structure.selection.selected', closeOptions);
});
</script>
