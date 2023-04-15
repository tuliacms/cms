export default class SelectionSubscriber {
    constructor(messenger, selection) {
        this.messenger = messenger;
        this.selection = selection;
    }

    static getSubscribedEvents() {
        return {
            'admin.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('editor.selection.select', (data) => self.select(data.id, data.type));
        this.messenger.receive('editor.selection.deselect', () => self.deselect());
        this.messenger.receive('editor.selection.hover', (data) => self.hover(data.id, data.type));
        this.messenger.receive('editor.selection.dehover', () => self.dehover());
    }

    select(id, type) {
        this.selection.select(id, type);
    }

    deselect() {
        this.selection.deselect();
    }

    hover(id, type) {
        this.selection.hover(id, type);
    }

    dehover() {
        this.selection.dehover();
    }
}
