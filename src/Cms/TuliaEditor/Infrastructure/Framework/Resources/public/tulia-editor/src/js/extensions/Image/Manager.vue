<template></template>

<script setup>
const { computed, defineProps, inject } = require('vue');
const props = defineProps(['instance']);
const extension = inject('extension.instance').manager('Image', props.instance);
const TuliaFilemanager = require('TuliaFilemanager');
const options = inject('options');

const filemanager = {
    instance: null
};

const getFilemanager = () => {
    if (filemanager.instance) {
        return filemanager.instance;
    }

    return filemanager.instance = TuliaFilemanager.create({
        showOnInit: false,
        endpoint: options.filemanager.endpoint,
        filter: { type: 'image' },
        multiple: false,
        closeOnSelect: true,
        onSelect: function (files) {
            if (!files.length) {
                return;
            }

            extension.execute('image-chosen', {
                id: files[0].id,
                filename: files[0].name
            });
        }
    });
};

extension.operation('chose-image', (data, success, fail) => {
    getFilemanager().open();
    success();
});
</script>

<script>
export default {
    name: 'ImageManager'
}
</script>
