export default class BlockHooks {
    messenger;
    eventsCollection = {};
    bindedBlocksHooks = [];

    constructor (messenger) {
        this.messenger = messenger;

        this.listen();
    }

    forBlock (blockId) {
        this.bindedBlocksHooks.push(blockId);

        let events = this.eventsCollection[blockId] ?? [];
        this.eventsCollection[blockId] = [];

        return new BlockHook(this.messenger, blockId, events);
    }

    listen () {
        this.messenger.on('structure.element.created', (type, id) => {
            if (type !== 'block') {
                return;
            }

            if (this.bindedBlocksHooks.indexOf(id) < 0) {
                if (!this.eventsCollection[id]) {
                    this.eventsCollection[id] = [];
                }

                this.eventsCollection[id].push({
                    event: 'created',
                });
            }
        });
    }
}

class BlockHook {
    messegner;
    blockId;
    occuredEvents;
    listeners = [];
    eventsCallsTimes = {
        created: 0
    };

    constructor (messegner, blockId, occuredEvents) {
        this.messegner = messegner;
        this.blockId = blockId;
        this.occuredEvents = occuredEvents;

        this.listen();
    }

    on (event, listener) {
        this.listeners.push({
            event: event,
            listener: listener
        });

        this.callListenersForOccuredEvents(event);
    }

    callListeners (event) {
        if (this.eventAlreadyDispatched(event) === false) {
            this.eventsCallsTimes[event] = 0;
        }

        if (this.isSingularEvent(event) && this.eventsCallsTimes[event] >= 1) {
            return;
        }

        this.eventsCallsTimes[event]++;

        for (let i in this.listeners) {
            if (this.listeners[i].event === event) {
                this.listeners[i].listener.call();
            }
        }
    }

    callListenersForOccuredEvents (event) {
        for (let i in this.occuredEvents) {
            if (this.occuredEvents[i].event === event) {
                this.callListeners(event);
            }
        }
    }

    listen () {

    }

    isSingularEvent (event) {
        switch (event) {
            case 'created':
                return true;
        }

        return false;
    }

    eventAlreadyDispatched (event) {
        return !!this.eventsCallsTimes[event];
    }
}
