import { watch } from "vue";

/**
 * Exports config from Admin to Editor
 */
export default class ConfigSynchronizer {
    constructor(messenger) {
        this.messenger = messenger;
    }

    sync(id, type, store) {
        watch(store, async (newValue) => {
            this.messenger.send('element.config.sync', {
                id: id,
                type: type,
                config: newValue.export,
            });
        }, { deep: true });
    }
}
