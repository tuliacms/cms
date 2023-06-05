export default class ElementDataSubscriber {
    constructor(messenger, elementDataRegistry, eventBus) {
        this.messenger = messenger;
        this.elementDataRegistry = elementDataRegistry;
        this.eventBus = eventBus;
    }

    static getSubscribedEvents() {
        return {
            'editor.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        /**
         * This event comes only when "save" and "cancel" editor,
         * to replace state of the Canvas as a whole. This MUST NOT be used to
         * set "data" from Admin to Editor. Data is edited only by Editor!
         */
        this.messenger.receive('element.data.replace', (data) => self.processDataReplace(data.id, data.type, data.data));
    }

    processDataReplace(id, type, data) {
        const dataStore = this.elementDataRegistry.get(id, type);

        dataStore.replace(data);
        this.eventBus.dispatch('element.data.replace', { id, type, data });
    }
}
