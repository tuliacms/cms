export default class SelectionSubscriber {
    constructor(messenger, selection) {
        this.messenger = messenger;
        this.selection = selection;
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('editor.selection.select', (data) => self.select(data.id, data.type));
    }

    select(id, type) {
        this.selection.select(id, type);
    }
}
