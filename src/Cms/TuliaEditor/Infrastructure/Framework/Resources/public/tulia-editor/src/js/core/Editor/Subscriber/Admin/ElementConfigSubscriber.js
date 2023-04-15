export default class ElementConfigSubscriber {
    constructor(messenger, elementConfigRegistry) {
        this.messenger = messenger;
        this.elementConfigRegistry = elementConfigRegistry;
    }

    static getSubscribedEvents() {
        return {
            'editor.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('element.config.sync', (data) => self.processChanged(data.id, data.type, data.config));
    }

    processChanged(id, type, config) {
        const configStore = this.elementConfigRegistry.get(id, type);

        configStore.replace(config);
    }
}
