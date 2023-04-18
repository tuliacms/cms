export default class EditorWindow {
    constructor(eventBus, view, structure) {
        this.eventBus = eventBus;
        this.view = view;
        this.structure = structure;
    }

    open() {
        this.view.open();
        this.structure.current();
        this.eventBus.dispatch('editor.opened');
    }

    cancel() {
        this.view.close();
        this.structure.revert();
        this.eventBus.dispatch('editor.canceled');
    }

    save() {
        this.view.close();
        const structure = this.structure.currentAsNew();
        this.eventBus.dispatch('editor.saved', { structure });
    }
}
