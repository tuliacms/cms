export default class ElementConfigSubscriber {
    constructor(messenger, elementConfigRegistry) {
        this.messenger = messenger;
        this.elementConfigRegistry = elementConfigRegistry;
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('element.config.sync', (data) => self.processChanged(data.id, data.type, data.config));
    }

    processChanged(id, type, config) {
        this.elementConfigRegistry.get(id, type).replace(config);
    }
}
