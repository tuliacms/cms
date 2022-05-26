export default class EventDispatcher {
    listeners = [];

    on (event, listener) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }

        this.listeners[event].push(listener);
    }

    emit (event, ...args) {
        if (!this.listeners[event]) {
            return;
        }

        for (let i in this.listeners[event]) {
            this.listeners[event][i].call(null, ...args);
        }
    }
}
