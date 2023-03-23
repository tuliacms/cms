export default class EventBus {
    constructor() {
        this.listeners = {};
    }

    listen(event, listener) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }

        this.listeners[event].push(listener);
    }

    dispatch(event, data) {
        if (this.listeners[event]) {
            for (let i in this.listeners[event]) {
                this.listeners[event][i].call(null, data);
            }
        }
    }
}
