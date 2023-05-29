import { watch } from "vue";

/**
 * Exports data from Editor to Admin
 */
export default class DataSynchronizer {
    constructor(messenger, eventBus) {
        this.messenger = messenger;
        this.eventBus = eventBus;
    }

    sync(id, type, store) {
        watch(store, (newValue) => {
            const data = {
                id: id,
                type: type,
                data: newValue.export,
            };

            this.messenger.send('element.data.sync', data);
            this.eventBus.dispatch('element.data.changed', data);
        }, { deep: true });
    }
}
