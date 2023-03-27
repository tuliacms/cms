export default class SelectionSubscriber {
    constructor(messenger, contextmenu) {
        this.messenger = messenger;
        this.contextmenu = contextmenu;
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('contextmenu.open', (data) => self.open(data.targets, data.position));
        this.messenger.receive('contextmenu.hide', () => self.hide());
    }

    open(targets, position) {
        this.contextmenu.openFromEditor(targets, position);
    }

    hide() {
        this.contextmenu.hide();
    }
}
