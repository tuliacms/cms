export default class Selection {
    constructor(messenger) {
        this.messenger = messenger;
    }

    select(id, type) {
        this.messenger.send('editor.selection.select', { id, type });
    }

    deselect() {
        this.messenger.send('editor.selection.deselect');
    }

    hover(id, type) {
        this.messenger.send('editor.selection.hover', { id, type });
    }

    dehover() {
        this.messenger.send('editor.selection.dehover');
    }
}
