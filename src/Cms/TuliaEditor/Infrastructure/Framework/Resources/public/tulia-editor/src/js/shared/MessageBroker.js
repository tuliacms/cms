module.exports = class MessageBroker {
    instanceId;
    windows = [];
    handledMessagesIds = [];

    constructor (instanceId, windows) {
        this.instanceId = parseInt(instanceId);
        this.windows = windows;
    }

    start () {
        window.addEventListener("message", (event) => {
            if (event.origin !== location.protocol + '//' + location.host) {
                return;
            }

            if (
                event.data.header
                && event.data.header.type === 'tulia-editor-message.messenger'
                && parseInt(event.data.header.instance) === this.instanceId
                && this.wasMessageHandled(event.data.header.messageId) === false
            ) {
                this.emitToAllWindows(event.data);
            }
        }, false);
    }

    emitToAllWindows (event) {
        for (let i in this.windows) {
            event.header.type = 'tulia-editor-message.broker';

            this.windows[i].postMessage(
                event,
                location.protocol + '//' + location.host
            );
        }
    }

    addWindow (newWindow) {
        this.windows.push(newWindow);
    }

    wasMessageHandled (id) {
        let position = this.handledMessagesIds.indexOf(id);

        this.handledMessagesIds.push(id);

        return position >= 0;
    }
};
