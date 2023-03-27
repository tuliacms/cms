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

    hover(id, type) {
        this.selection.hover(id, type);
        this.update();
    }

    dehover() {
        this.selection.dehover();
        this.update();
    }

    disableSelection() {
        this.selection.disableSelection();
        this.update();
    }

    enableSelection() {
        this.selection.enableSelection();
        this.update();
    }

    disableHovering() {
        this.selection.disableHovering();
        this.update();
    }

    enableHovering() {
        this.selection.enableHovering();
        this.update();
    }

    update() {
        this.messenger.send('admin.selection.changed', {
            selection: this.selection.export,
        });
    }
}
