<template>
    <div>
        <div class="fm-files-list">
            <div
                class="fm-file-outer"
                v-for="file in files.list"
                @click.stop="clickOnFile(file)"
                @contextmenu.prevent="(event) => contextmenuOfFile(event, file.id)"
            >
                <div
                    v-if="file.type === 'directory'"
                    :class="{ 'fm-file': true, 'fm-file-active': file.selected }"
                    data-class="'fm-file-type-' + file.type"
                    data-type="directory"
                    :data-file-id="file.id"
                >
                    <div class="fm-file-inner">
                        <div class="fm-file-preview"><span class="fm-file-image"></span></div>
                        <div class="fm-file-name" :title="file.name">{{ file.name }}</div>
                    </div>
                </div>
                <div
                    v-else
                    :class="{ 'fm-file': true, 'fm-file-active': file.selected }"
                    data-class="'fm-file-type-' + file.type"
                    data-type="file"
                    :data-file-id="file.id"
                >
                    <div class="fm-file-inner">
                        <div class="fm-file-extension">{{ file.extension }}</div>
                        <div class="fm-file-preview">
                            <span class="fm-file-check"></span>
                            <span class="fm-file-image" :style="{ 'background-image': 'url(' + file.preview + ')' }"></span>
                        </div>
                        <div class="fm-file-name" :title="file.name">{{ file.name }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div :class="{ 'fm-files-list-loader': true, 'fm-active': view.loading }"></div>
    </div>
</template>

<script setup>
const axios = require('axios').default;
const { defineProps, onMounted, reactive, inject, watch, toRaw } = require('vue');

const eventDispatcher = inject('eventDispatcher');
const commandBus = inject('commandBus');
const selection = inject('selection');
const props = defineProps(['options']);

const view = reactive({
    loading: true,
});

const files = reactive({
    list: [],
    delete: (ids) => {
        if (!Array.isArray(ids)) {
            ids = [ids];
        }

        for (let f in files.list) {
            for (let i in ids) {
                if (files.list[f].id === ids[i]) {
                    files.list.splice(f, 1);
                }
            }
        }
    },
    fetch: (ids) => {
        let list = [];

        for (let f in files.list) {
            for (let i in ids) {
                if (files.list[f].id === ids[i]) {
                    list.push(toRaw(files.list[f]));
                }
            }
        }

        return list;
    }
});

const clicks = {
    times: 0,
    timer: null
};
const clickOnFile = (file) => {
    commandBus.execute('contextmenu', null);

    clicks.times++;

    if (clicks.times === 1) {
        toggleSelection(file.id, file.name, file.size);
        setTimeout(() => {
            clicks.times = 0;
        }, 200);
    } else {
        selectFile(file.id);
        clicks.times = 0;
    }
};

const updateSelection = () => {
    let info = {
        size: 0,
        files: []
    };

    for (let i in files.list) {
        if (selection.isSelected(files.list[i].id)) {
            files.list[i].selected = true;
            info.size += files.list[i].size;
            info.files.push(files.list[i]);
        } else {
            files.list[i].selected = false;
        }
    }

    let status = '';

    if (info.files.length === 1) {
        status = 'Selected: <b>' + info.files[0].name + '</b> <i>' + info.files[0].size_formatted + '</i>';
    } else if (info.files.length > 1) {
        status = 'Selected files: ' + info.files.length;
    }

    commandBus.execute('status', status);
};

const selectFile = (id) => {
    selection.clear();
    selection.select(id);
    updateSelection();
    commandBus.execute('select');
};

const toggleSelection = (id) => {
    selection.toggle(id);
    updateSelection();
};

const refreshFilesList = () => {
    view.loading = true;
    files.list = [];

    let filter = '';

    axios.post(props.options.endpoint + '?cmd=ls&filter=' + filter)
        .then(function (response) {
            files.list = response.data;
            updateSelection();
            view.loading = false;
        })
        .catch(function (error) {
            console.log(error);
        });
};

const appendFiles = (sourceFiles) => {
    for (let i in sourceFiles) {
        files.list.unshift(sourceFiles[i]);
        eventDispatcher.dispatch('files.list.new', sourceFiles[i].id);
    }
};

const deleteFile = (ids) => {
    Tulia.Confirmation.confirm({
        title: `You really want do delete selected ${ids.length} file(s)?`
    }).then((v) => {
        if (!v.value) {
            return;
        }

        axios.post(props.options.endpoint + '?cmd=delete&id=' + ids.join(','))
            .then(function (response) {
                files.delete(ids);
                selection.deselect(ids);
            })
            .catch(function (error) {
                console.log(error);
            });
    });
};

const contextmenuItems = (items, id) => {
    items.push({
        onClick: () => commandBus.execute('files.list.delete', id),
        label: 'Delete file'
    });
};

const contextmenuOfFile = (event, fileId) => {
    selection.toggle(fileId);
    commandBus.execute('contextmenu', event, 'file', selection.getSelected());
};

const select = function () {
    const selected = selection.getSelected();
    const filesList = files.fetch(selected);

    if (props.options.targetInput) {
        const input = document.querySelector(props.options.targetInput);

        if (selected[0]) {
            input.value = selected[0];
        } else {
            input.value = null;
        }

        input.dispatchEvent(new Event('change'));
    } else if (props.options.onSelect) {
        props.options.onSelect(filesList);
    }

    eventDispatcher.dispatch('files.selected', selected, filesList);
};

onMounted(() => {
    refreshFilesList();

    commandBus.command('select', select);
    commandBus.command('files.list.refresh', refreshFilesList);
    commandBus.command('contextmenu.items.file', contextmenuItems);
    commandBus.command('files.list.delete', deleteFile);
    eventDispatcher.on('opened', () => {
        clicks.times = 0;
    });
    eventDispatcher.on('upload.uploaded', appendFiles);
    eventDispatcher.on('selection.change', updateSelection);
});
</script>
