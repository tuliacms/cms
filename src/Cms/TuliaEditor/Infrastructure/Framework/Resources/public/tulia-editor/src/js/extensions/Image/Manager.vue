<template></template>
<script setup>
const { computed, defineProps, inject } = require('vue');
const props = defineProps(['instanceId']);
const extension = inject('instance.extensions').manager('Image', props.instanceId);
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

            extension.send('image-chosen', {
                id: files[0].id,
                filename: files[0].name,
            });
        }
    });
};

extension.receive('chose-image', () => {
    getFilemanager().open();
});
extension.receive('remove-image', () => {
    getFilemanager().clearSelection();
    extension.send('image-chosen', {
        id: null,
        filename: null,
    });
});
</script>
<script>
export default {name: 'Extension.Image.Manager'}
</script>
