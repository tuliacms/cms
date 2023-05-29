export default class SelectionSubscriber {
    constructor(selection) {
        this.selection = selection;
    }

    static getSubscribedEvents() {
        return {
            'editor.canceled': 'clear',
            'editor.opened': 'clear',
            'editor.saved': 'clear',
        };
    }

    clear() {
        this.selection.dehover();
        this.selection.deselect();
    }
}
