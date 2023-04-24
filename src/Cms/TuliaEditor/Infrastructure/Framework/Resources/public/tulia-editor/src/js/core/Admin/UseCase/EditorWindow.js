export default class EditorWindow {
    constructor(eventBus, view, structure, assets, structureRenderer) {
        this.eventBus = eventBus;
        this.view = view;
        this.structure = structure;
        this.assets = assets;
        this.structureRenderer = structureRenderer;
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
        this.eventBus.dispatch('editor.saved', {
            source: this.structure.currentAsNew(),
            content: this.structureRenderer.render(),
            assets: this.assets.collectNames(),
        });
    }
}
