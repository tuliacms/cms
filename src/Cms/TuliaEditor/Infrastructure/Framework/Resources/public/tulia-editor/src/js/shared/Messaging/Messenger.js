const OperationPromise = require('shared/Messaging/OperationPromise.js').default;
const Executor = require('shared/Messaging/OperationExecutor.js').default;
const { v4 } = require('uuid');

export default class Messenger {
    instanceId;
    windowName;
    windows;
    notificationListeners = [];
    operationListeners = [];
    promises = [];

    constructor (instanceId, windowName, windows) {
        this.instanceId = parseInt(instanceId);
        this.windowName = windowName;
        this.windows = windows;

        this.start();
    }

    start () {
        window.addEventListener('message', (event) => {
            if (event.origin !== location.protocol + '//' + location.host) {
                return;
            }

            if (!event.data.header) {
                return;
            }

            if (event.data.header.instanceId !== this.instanceId) {
                return;
            }

            if (event.data.header.type === 'notification') {
                this.callNotificationListeners(event.data);
            }
            if (event.data.header.type === 'operation') {
                this.callOperationListeners(event.data);
            }
            if (event.data.header.type === 'operation-confirmation') {
                this.confirmOperation(event.data);
            }

        }, false);
    }

    /**
     * Executes operation, and waits for all listeners in all windows to confirm
     * executed operation, and then call caller when end.
     */
    execute (operation, body) {
        const messageId = v4();
        const promise = new OperationPromise(operation, body, this.windows.length);

        this.promises.push({
            messageId: messageId,
            operation: operation,
            promise: promise,
        });

        this.sendToAllWindows(operation, 'operation', messageId, body);

        return promise.getNativePromise();
    }

    /**
     * Sends one way informational event, without delivering confirmation.
     */
    notify (notification, ...body) {
        this.sendToAllWindows(notification, 'notification', v4(), body);
    }

    /**
     * Bind listeners for notify() calls.
     */
    on (notification, listener) {
        if (!this.notificationListeners[notification]) {
            this.notificationListeners[notification] = [];
        }

        this.notificationListeners[notification].push({
            listener: listener,
        });
    }

    operation (operation, listener) {
        if (!this.operationListeners[operation]) {
            this.operationListeners[operation] = [];
        }

        this.operationListeners[operation].push({
            listener: listener
        });
    }

    callOperationListeners (event) {
        if (!this.operationListeners[event.header.name]) {
            // If no listeners registered, we can confirm operation immediately
            this.sendOperationConfirmation(event.header.name, event.header.messageId, {});
            return;
        }

        new Executor(
            event,
            this.operationListeners[event.header.name],
            // Success
            (event, data) => {
                this.sendOperationConfirmation(event.header.name, event.header.messageId, data);
            },
            // Fail
            () => {

            }
        );
    }

    callNotificationListeners (event) {
        for (let i in this.notificationListeners[event.header.name]) {
            let listener = this.notificationListeners[event.header.name][i];
            listener.listener.call(this, ...event.body);
        }
    }

    sendOperationConfirmation (operation, messageId, body) {
        this.sendToAllWindows(operation, 'operation-confirmation', messageId, body);
    }

    confirmOperation (event) {
        for (let i in this.promises) {
            if (
                this.promises[i].operation === event.header.name
                && this.promises[i].messageId === event.header.messageId
            ) {
                this.promises[i].promise.resolve(event.body);
            }
        }
    }

    addWindow (window) {
        this.windows.push(window);
    }

    sendToAllWindows (name, type, messageId, body) {
        for (let i in this.windows) {
            this.windows[i].postMessage(
                {
                    header: {
                        name: name,
                        instanceId: this.instanceId,
                        type: type,
                        messageId: messageId,
                        sender: this.windowName
                    },
                    body: body
                },
                location.protocol + '//' + location.host
            );
        }
    }
}
