<template>
    <table class="table table-sm table-hover">
        <tbody>
            <tr>
                <th>All messages count</th>
                <td class="text-end">{{ messages.all }}</td>
            </tr>
            <tr>
                <td>&nbsp; &nbsp; of notifications</td>
                <td class="text-end">{{ messages.notifications }}</td>
            </tr>
            <tr>
                <td>&nbsp; &nbsp; of operations</td>
                <td class="text-end">{{ messages.operations }}</td>
            </tr>
            <tr>
                <td>&nbsp; &nbsp; of operation confirmations</td>
                <td class="text-end">{{ messages.operationConfirmations }}</td>
            </tr>
            <tr>
                <td class="text-muted">&nbsp; &nbsp; of last messages</td>
                <td class="text-end text-muted">{{ messages.list.length }}</td>
            </tr>
        </tbody>
    </table>
    <div class="text-center">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tued-debug-messenger">
            Show {{ messages.list.length }} last messages
        </button>&nbsp;
        <button type="button" class="btn btn-sm btn-secondary" @click="messages.list = []">
            Clear messages
        </button>
    </div>
    <Teleport to=".tued-editor-window">
        <div class="modal fade" id="tued-debug-messenger" tabindex="-1" @click.stop="" @mouseup.stop="" @mousedown.stop="">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Messenger messages list</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-4">
                                <label>Filter type</label>
                                <select v-model="filter.type" class="form-select form-select-sm">
                                    <option v-for="(type, i) in filter.types" :key="i" :value="type">{{ type }}</option>
                                </select>
                            </div>
                            <div class="col-4">
                                <label>Filter name</label>
                                <select v-model="filter.name" class="form-select form-select-sm">
                                    <option v-for="(name, i) in filter.names" :key="i" :value="name">{{ name }}</option>
                                </select>
                            </div>
                        </div>
                        <div v-for="(msg, i) in messages.list" :key="i">
                            <div v-if="
                                (!filter.name || filter.name === msg.header.name)
                                && (!filter.type || filter.type === msg.header.type)
                            ">
                                <div class="mb-3">
                                    <span class="badge badge-info">{{ msg.header.type }}</span>
                                    of <span class="badge badge-primary">{{ msg.header.name }}</span>
                                    from <span class="badge badge-secondary">{{ msg.header.sender }}</span>
                                    ID {{ msg.header.messageId }}
                                    Editor: {{ msg.header.instanceId }}
                                </div>
                                <pre>{{ msg.body }}</pre>
                                <hr />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" @click="messages.list = []">Clear log</button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
const { inject, computed, reactive } = require('vue');
const messenger = inject('messenger');

const messages = reactive({
    all: 0,
    notifications: 0,
    operations: 0,
    operationConfirmations: 0,
    lastCount: 40,
    list: [],
});
const filter = reactive({
    name: null,
    names: [],
    type: null,
    types: ['', 'operation', 'operation-confirmation', 'notification'],
});

const updateFilters = () => {
    filter.names = [''];

    for (let i in messages.list) {
        const name = messages.list[i].header.name;

        if (!filter.names.includes(name)) {
            filter.names.push(name);
        }
    }
};

messenger.onMessageReceive((message) => {
    messages.all++;

    if (message.header.type === "notification") {
        messages.notifications++;
    }
    if (message.header.type === "operation") {
        messages.operations++;
    }
    if (message.header.type === "operation-confirmation") {
        messages.operationConfirmations++;
    }

    messages.list.push(message);

    if (messages.list.length > messages.lastCount) {
        messages.list.shift();
    }

    updateFilters();
});
</script>
