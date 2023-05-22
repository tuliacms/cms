<template></template>

<script setup>
const { inject, defineProps } = require('vue');
const Tulia = require('Tulia');
const options = inject('options');
const props = defineProps(['instanceId']);
const extension = inject('instance.extensions').manager('BackgroundImage', props.instanceId);
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

extension.receive('chose-image', data => {
    getFilemanager().open();
});
extension.receive('remove-image', data => {
    getFilemanager().clearSelection();
    extension.send('image-chosen', {
        id: null,
        filename: null,
    });
});
</script>
<script>
export default {name: 'Extension.BackgroundImage.Manager'}
</script>
