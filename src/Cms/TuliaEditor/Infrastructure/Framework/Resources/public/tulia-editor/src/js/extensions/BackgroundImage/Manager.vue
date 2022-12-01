<template></template>

<script setup>
const { inject, defineProps } = require('vue');
const Tulia = require('Tulia');
const options = inject('options');
const props = defineProps(['instance']);
const extension = inject('extension.instance')
    .manager('BackgroundImage', props.instance);
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
                filename: files[0].name,
            });
        }
    });
};

extension.operation('chose-image', (data, success, fail) => {
    getFilemanager().open();
    success();
});
extension.operation('remove-image', (data, success, fail) => {
    getFilemanager().clearSelection();
    extension.execute('image-chosen', {
        id: null,
        filename: null,
    });
    success();
});
</script>

<script>
export default {
    name: 'BackgroundImageManager'
}
</script>
