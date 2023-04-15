export default class SelectionSubscriber {
    constructor(selection, messenger, eventBus) {
        this.selection = selection;
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

        this.messenger.receive('admin.selection.changed', (data) => self.processChanged(data.selection));
    }

    processChanged(selection) {
        const before = this.selection.export();
        this.selection.update(selection);
        const after = this.selection.export();

        if (before.selected.id !== after.selected.id) {
            if (after.selected.id) {
                this.eventBus.dispatch('selection.selected', { id: after.selected.id, type: after.selected.type });
            } else {
                this.eventBus.dispatch('selection.deselected');
            }
        }
        if (before.hovered.id !== after.hovered.id) {
            if (after.hovered.id) {
                this.eventBus.dispatch('selection.hovered', { id: after.hovered.id, type: after.hovered.type });
            } else {
                this.eventBus.dispatch('selection.dehovered');
            }
        }
    }
}
