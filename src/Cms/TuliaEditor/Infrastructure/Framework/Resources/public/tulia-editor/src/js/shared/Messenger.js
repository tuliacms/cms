module.exports = class Messanger {
    instanceId;
    brokerWindow;
    code;
    messageIdGlobal = 0;
    handledMessagesIds = [];
    listeners = {};

    constructor (instanceId, brokerWindow, code) {
        this.instanceId = parseInt(instanceId);
        this.brokerWindow = brokerWindow;
        this.code = code;

        this.brokerWindow.addEventListener("message", (event) => {
            if (event.origin !== location.protocol + '//' + location.host) {
                return;
            }

            if (
                event.data.header
                && event.data.header.type === 'tulia-editor-message.broker'
                && parseInt(event.data.header.instance) === this.instanceId
                && this.wasMessageHandled(event.data.header.messageId) === false
            ) {
                this.callListeners(event.data);
            }
        }, false);
    }

    send (name, body) {
        this.brokerWindow.postMessage(
            {
                header: {
                    type: 'tulia-editor-message.messenger',
                    name: name,
                    instance: this.instanceId,
                    messageId: this.generateMessageId(name)
                },
                body: body
            },
            location.protocol + '//' + location.host
        );
    }

    listen (name, callback) {
        if (!this.listeners[name]) {
            this.listeners[name] = [];
        }

        this.listeners[name].push(callback);
    }

    callListeners (event) {
        for (let i in this.listeners[event.header.name]) {
            this.listeners[event.header.name][i].call(null, event.body);
        }
    }

    generateMessageId (name) {
        return `message-id-${this.code}-${name}-${this.getNextMessageId()}`;
    }

    getNextMessageId () {
        return ++this.messageIdGlobal;
    }

    wasMessageHandled (id) {
        let position = this.handledMessagesIds.indexOf(id);

        this.handledMessagesIds.push(id);

        return position >= 0;
    }
};
