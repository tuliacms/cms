export default class Selection {
    constructor(messenger) {
        this.messenger = messenger;
    }

    select(id, type) {
        this.messenger.send('editor.selection.select', { id, type });
    }

    deselect() {

    }
}
