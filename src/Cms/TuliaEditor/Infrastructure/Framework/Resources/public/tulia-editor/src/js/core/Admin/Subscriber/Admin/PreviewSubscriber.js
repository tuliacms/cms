export default class PreviewSubscriber {
    constructor(preview, structureRenderer) {
        this.preview = preview;
        this.structureRenderer = structureRenderer;
    }

    static getSubscribedEvents() {
        return {
            'admin.view.ready': 'renderPreview',
            'editor.saved': 'refreshPreviewOnSave',
            'preview.ready': 'refreshPreviewOnReady',
        };
    }

    renderPreview() {
        this.preview.render();
    }

    refreshPreviewOnSave({ source, content }) {
        this.preview.refresh(content);
    }

    refreshPreviewOnReady() {
        this.preview.refresh(this.structureRenderer.render());
    }
}
