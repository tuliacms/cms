export default class ElementDataSubscriber {
    constructor(messenger, elementDataRegistry) {
        this.messenger = messenger;
        this.elementDataRegistry = elementDataRegistry;
    }

    static getSubscribedEvents() {
        return {
            'admin.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('element.data.sync', (data) => self.processDataSync(data.id, data.type, data.data));
    }

    processDataSync(id, type, data) {
        const dataStore = this.elementDataRegistry.get(id, type);

        dataStore.replace(data);
    }
}
