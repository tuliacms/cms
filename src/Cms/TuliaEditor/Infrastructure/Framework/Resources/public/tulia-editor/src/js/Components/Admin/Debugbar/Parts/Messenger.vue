<template>
    <div class="px-4">
        <p>
            All messages count: <span class="badge badge-info">{{ messagesCount.all }}</span><br />
            Notifications count: <span class="badge badge-info">{{ messagesCount.notifications }}</span><br />
            Operations count: <span class="badge badge-info">{{ messagesCount.operations }}</span><br />
        </p>
    </div>
</template>

<script setup>
const { inject, computed, reactive } = require('vue');
const messenger = inject('messenger');

const messagesCount = reactive({
    all: 0,
    notifications: 0,
    operations: 0,
});

messenger.onMessageReceive((message) => {
    messagesCount.all++;

    if (message.header.type === "notification") {
        messagesCount.notifications++;
    }
    if (message.header.type === "operation") {
        messagesCount.operations++;
    }
});
</script>
