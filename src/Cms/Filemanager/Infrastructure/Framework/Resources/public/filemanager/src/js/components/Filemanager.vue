<template>
    <div :class="{ 'tulia-filemanager': true, 'fm-opened': view.opened }">
        <div class="fm-bg" @click="close()"></div>
        <div class="fm-fg">
            <div class="fm-toolbar">
                <div class="fm-upload-btn">
                    <button type="button" class="btn btn-primary">
                        <span>Upload file</span>
                        <input type="file" ref="fileupload" class="fm-upload" multiple @change="uploadFile" />
                    </button>
                </div>
                <div class="fm-buttons">
<!--                    <button type="button" class="btn btn-outline-secondary btn-icon-only" data-cmd="back" title="Go back"><i class="btn-icon fas fa-chevron-left"></i></button>-->
                    <button type="button" class="btn btn-outline-secondary btn-icon-only" title="Refresh" @click="commandBus.execute('files.list.refresh')"><i class="btn-icon fas fa-sync-alt"></i></button>
<!--                    <button type="button" class="btn btn-outline-secondary btn-icon-only" data-action="new-directory" title="New directory"><i class="btn-icon fas fa-folder-plus"></i></button>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-outline-secondary btn-icon-only" type="button" data-bs-toggle="dropdown" title="Order by"><i class="btn-icon fas fa-sort-alpha-down"></i></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item active dropdown-item-with-icon" href="#" data-cmd="order-by-created-desc"><i class="dropdown-icon fas fa-sort-amount-down-alt"></i>Newest first</a>
                            <a class="dropdown-item dropdown-item-with-icon" href="#" data-cmd="order-by-created-asc"><i class="dropdown-icon fas fa-sort-amount-down"></i>Oldest first</a>
                            <a class="dropdown-item dropdown-item-with-icon" href="#" data-cmd="order-by-name-asc"><i class="dropdown-icon fas fa-sort-alpha-down"></i>By name</a>
                            <a class="dropdown-item dropdown-item-with-icon" href="#" data-cmd="order-by-name-desc"><i class="dropdown-icon fas fa-sort-alpha-down-alt"></i>By name</a>
                        </div>
                    </div>-->
                </div>
                <div class="fm-close">
                    <button type="button" @click="close()"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <div class="fm-tree">
                <div class="fm-directory-tree"></div>
            </div>
            <div class="fm-body" @click="selection.clear()">
                <FilesList :options="options"></FilesList>
            </div>
            <div class="fm-bottombar">
                <div :class="{ 'fm-upload-progressbar': true, 'fm-hidden': view.progressbar === null }">
                    <span class="fm-meter" :style="{ width: view.progressbar + '%' }"></span>
                </div>
                <div class="fm-status" v-html="view.status"></div>
                <div class="fm-select-btn">
                    <button type="button" class="btn btn-primary btn-icon-left" :disabled="!anyFileIsSelected" @click="select()"><i class="btn-icon fas fa-check"></i>Select</button>
                </div>
            </div>
        </div>
        <div :class="{ 'fm-dragondrop-area': true, 'fm-hidden': !view.dragonDropActive }"><div><div><div>Upuść pliki tutaj...</div></div></div></div>

        <Teleport to="body">
            <ul class="dropdown-menu show fm-contextmenu" v-if="contextmenu.opened" :style="{ top: contextmenu.position.y + 'px', left: contextmenu.position.x + 'px' }">
                <li v-for="item in contextmenu.items">
                    <a class="dropdown-item dropdown-item-with-icon dropdown-item-danger" href="#" @click="item.onClick"><i class="dropdown-icon fas fa-trash"></i> {{ item.label }}</a>
                </li>
            </ul>
        </Teleport>
    </div>
</template>

<script setup>
const FilesList = require('components/FilesList.vue').default;
const { defineProps, onMounted, provide, reactive, computed, ref } = require('vue');

const props = defineProps([
    'options',
    'callbacks',
    'services',
]);

provide('eventDispatcher', props.services.eventDispatcher);
provide('commandBus', props.services.commandBus);
provide('selection', props.services.selection);
const selection = props.services.selection;
const commandBus = props.services.commandBus;
const eventDispatcher = props.services.eventDispatcher;

const view = reactive({
    active: false,
    opened: false,
    dragonDropActive: false,
    progressbar: null,
    status: ''
});

const close = function () {
    view.opened = false;
    view.active = false;
    eventDispatcher.dispatch('closed');
};
const open = function () {
    view.opened = true;
    view.active = true;
    eventDispatcher.dispatch('opened');
};

onMounted(() => {
    commandBus.command('close', close);
    commandBus.command('open', open);
    commandBus.command('status', (status) => view.status = status);

    if (props.options.targetInput) {
        let input = document.querySelector(props.options.targetInput);

        if (input) {
            if (input.value) {
                props.services.selection.select(input.value);
            }
        }
    }

    if (props.options.showOnInit) {
        open();
    }
});




/*************
 * Selection
 ************/
const closeOnSelection = function () {
    if (props.options.closeOnSelect) {
        close();
    }
};

const anyFileIsSelected = ref(false);
const updateSelectVisibility = () => {
    anyFileIsSelected.value = selection.getSelected().length !== 0;
};

onMounted(() => {
    eventDispatcher.on('selection.change', updateSelectVisibility);
    eventDispatcher.on('files.selected', closeOnSelection);
    eventDispatcher.on('files.list.new', (id) => selection.select(id));
});





/***************
 * Contextmenu
 **************/
const contextmenu = reactive({
    items: [],
    context: null,
    opened: false,
    position: {
        x: 0,
        y: 0
    }
});
const openContextmenu = (event, type, context) => {
    if (!event) {
        return;
    }

    contextmenu.context = context;
    contextmenu.type = type;
    contextmenu.items = [];

    commandBus.execute('contextmenu.items', contextmenu.items, contextmenu.type, contextmenu.context);
    commandBus.execute('contextmenu.items.' + contextmenu.type, contextmenu.items, contextmenu.context);

    contextmenu.position.x = event.x;
    contextmenu.position.y = event.y;
    contextmenu.opened = contextmenu.items.length >= 1;
};

const hideContextmenu = () => {
    contextmenu.opened = false;
};

onMounted(() => {
    commandBus.command('contextmenu', openContextmenu);
    document.body.addEventListener('click', hideContextmenu);
});




/*************
 * Uploading
 ************/
const Uploader = require('shared/Uploader.js').default;
const fileupload = ref('fileupload');
const uploader = new Uploader((cmd) => {
    return props.options.endpoint + '?cmd=' + cmd;
});

const createDragonDrop = () => {
    if (!FileAPI.support.dnd) {
        return;
    }

    $(document).dnd(function (over) {
        view.dragonDropActive = !!over;
    }, function (files) {
        if (!view.active) {
            return;
        }

        if(files.length) {
            uploader.upload(files, null, progressbar);
        }
    });
};

const uploadFile = (e) => {
    uploader.upload(FileAPI.getFiles(e), null, progressbar);
    fileupload.value = null;
};

const progressbar = (percentage, uploadedFiles) => {
    if (percentage === 0) {
        view.progressbar = 0;
    } else if (percentage === 100) {
        eventDispatcher.dispatch('upload.uploaded', uploadedFiles);
        view.progressbar = 100;

        setTimeout(function () {
            view.progressbar = null;
        }, 500);
    } else {
        view.progressbar = percentage;
    }
};

onMounted(() => {
    createDragonDrop();
});
</script>
