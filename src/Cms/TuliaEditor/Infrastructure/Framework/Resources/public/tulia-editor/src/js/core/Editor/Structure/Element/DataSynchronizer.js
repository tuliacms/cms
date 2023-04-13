import { watch } from "vue";

/**
 * Exports data from Editor to Admin
 */
export default class DataSynchronizer {
    constructor(messenger) {
        this.messenger = messenger;
    }

    sync(id, type, store) {
        watch(store, async (newValue) => {
            this.messenger.send('element.data.sync', {
                id: id,
                type: type,
                data: newValue.export,
            });
        }, { deep: true });
    }
}
