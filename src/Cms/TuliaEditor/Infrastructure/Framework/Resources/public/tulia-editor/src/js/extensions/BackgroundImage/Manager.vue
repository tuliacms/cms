<template>
    <div class="mb-3">
        <label class="form-label">{{ props.label }}</label>
        <div>
            <button class="btn btn-primary btn-icon-left" type="button" @click="props.block.execute('chose-image', { placement: props.placement })">
                <i class="btn-icon fas fa-image"></i>
                {{ translator.trans('choseImage') }}
            </button>
        </div>
    </div>
</template>

<script setup>
const { computed, onMounted, defineProps, inject, reactive } = require('vue');

const props = defineProps({
    block: {
        required: true,
        type: Object
    },
    label: {
        required: true,
        type: String
    },
    modelValue: {
        required: true,
        type: Object
    },
    placement: {
        required: false,
        type: String,
        default () {
            return 'image';
        }
    }
});
const Tulia = require('Tulia');
const options = inject('options');
const translator = inject('translator');

const modelValue = reactive(props.modelValue);
const filemanager = {
    instance: null
};

const getFilemanager = () => {
    if (filemanager.instance) {
        return filemanager.instance;
    }

    return filemanager.instance = Tulia.Filemanager.create({
        showOnInit: false,
        endpoint: options.filemanager.endpoint,
        filter: { type: 'image' },
        multiple: false,
        closeOnSelect: true,
        onSelect: function (files) {
            if (!files.length) {
                return;
            }

            modelValue.id = files[0].id;
            modelValue.filename = files[0].name;
        }
    });
};

onMounted(() => {
    props.block.operation('chose-image', (data, success, fail) => {
        if (data.placement === props.placement) {
            getFilemanager().show();
        }
        success();
    });
});
</script>

<script>
export default {
    name: 'BackgroundImageManagerControl'
}
</script>
