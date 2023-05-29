export default class StructureSubscriber {
    constructor(structure, messenger, eventBus) {
        this.structure = structure;
        this.messenger = messenger;
        this.eventBus = eventBus;
    }

    static getSubscribedEvents() {
        return {
            'editor.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('admin.structure.changed', (data) => self.processChanged(data.structure));
    }

    processChanged(newStructure) {
        this.structure.update(newStructure);
        this.eventBus.dispatch('structure.changed');
    }
}
