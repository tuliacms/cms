export default class EventDispatcher {
    events = [];

    on (events, listener, priority) {
        events = events.split(',');
        priority = priority || 100;

        for (let i = 0; i < events.length; i++) {
            let name = events[i].trim();

            if (this.events[name]) {
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            } else {
                this.events[name] = [];
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            }

            this.events[name].sort(function (a, b) {
                return b.priority - a.priority;
            });
        }

        return this;
    }

    emit (name, ...args) {
        if (! this.events[name]) {
            return this;
        }

        args = args || [];

        for (let i = 0; i < this.events[name].length; i++) {
            if (typeof(this.events[name][i].listener) !== 'function') {
                throw new Error('One of the listeners of the "' + name + '" event is not a function.');
            }

            this.events[name][i].listener(...args);
        }

        return this;
    }
};
