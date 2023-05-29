import ContextmenuManager from "core/Admin/Contextmenu/Contextmenu";

export default class Contextmenu {
    constructor(store, selection) {
        this.contextmenu = new ContextmenuManager(store);
        this.selection = selection;
    }

    open(event) {
        this.contextmenu.open(event);
    }

    openFromEditor(targets, position) {
        this.contextmenu.openFromEditor(targets, position);
    }

    hide() {
        this.contextmenu.hide();
    }

    setEditorOffsetProvider(provider) {
        this.contextmenu.setEditorOffsetProvider(provider);
    }

    register(elementId, type, data) {
        return this.contextmenu.register(elementId, type, data);
    }

    items(id, type, callback) {
        this.contextmenu.items(id, type, callback);
    }
}
