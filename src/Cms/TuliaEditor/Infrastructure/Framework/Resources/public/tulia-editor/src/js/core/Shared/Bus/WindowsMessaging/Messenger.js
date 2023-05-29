const { v4 } = require('uuid');

export default class Messenger {
    constructor(instanceId, sender, receiver) {
        this.instanceId = parseInt(instanceId);
        this.sender = sender;
        this.receiver = receiver;
        this.listeners = {};

        this.start();
    }

    setDestinationWindow(destinationWindow) {
        this.destinationWindow = destinationWindow;
    }

    receive(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }

        this.listeners[event].push(callback);
    }

    send(event, body) {
        this.destinationWindow.postMessage(
            {
                header: {
                    event: event,
                    instanceId: this.instanceId,
                    messageId: v4(),
                    sender: this.sender,
                },
                body: body,
            },
            location.protocol + '//' + location.host
        );
    }

    start() {
        window.addEventListener('message', (event) => {
            if (
                event.origin !== location.protocol + '//' + location.host
                || !event.data.header
                || event.data.header.instanceId !== this.instanceId
                || event.data.header.sender !== this.receiver
            ) {
                return;
            }

            if (!this.listeners[event.data.header.event]) {
                return;
            }

            for (let i in this.listeners[event.data.header.event]) {
                this.listeners[event.data.header.event][i].call(null, event.data.body);
            }
        }, false);
    }
}
