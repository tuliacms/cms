export default class ElementConfigSubscriber {
    constructor(messenger, elementConfigRegistry, eventBus) {
        this.messenger = messenger;
        this.elementConfigRegistry = elementConfigRegistry;
        this.eventBus = eventBus;
    }

    static getSubscribedEvents() {
        return {
            'editor.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('element.config.sync', (data) => self.processConfigSync(data.id, data.type, data.config));
    }

    processConfigSync(id, type, config) {
        const configStore = this.elementConfigRegistry.get(id, type);

        configStore.replace(config);
        this.eventBus.dispatch('element.config.sync', { id, type, config });
    }
}
