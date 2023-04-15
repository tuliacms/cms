export default class EditorWindow {
    constructor(eventBus, view) {
        this.eventBus = eventBus;
        this.view = view;
    }

    static getSubscribedEvents() {
        return {
            //'preview.clicked': 'open',
        };
    }

    cancel() {
        this.view.close();
        this.eventBus.dispatch('editor.canceled');
    }

    open() {
        this.view.open();
        this.eventBus.dispatch('editor.opened');
    }

    save() {
        this.view.close();
        this.eventBus.dispatch('editor.saved');
    }
}
