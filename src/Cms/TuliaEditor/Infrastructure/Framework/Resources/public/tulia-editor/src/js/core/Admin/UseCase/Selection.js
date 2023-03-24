export default class Selection {
    constructor(selection, messenger) {
        this.selection = selection;
        this.messenger = messenger;
    }

    select(id, type) {
        this.selection.select(id, type);
        this.update();
    }

    deselect() {
        this.selection.deselect();
        this.update();
    }

    update() {
        this.messenger.send('admin.selection.changed', {
            selection: this.selection.export,
        });
    }
}
