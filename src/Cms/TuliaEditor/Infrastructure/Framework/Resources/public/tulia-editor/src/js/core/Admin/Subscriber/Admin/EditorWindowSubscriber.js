export default class EditorWindowSubscriber {
    constructor(editorWindow) {
        this.editorWindow = editorWindow;
    }

    static getSubscribedEvents() {
        return {
            'preview.clicked': 'openEditor',
        };
    }

    openEditor() {
        this.editorWindow.open();
    }
}
