export default class RenderedContentSubscriber {
    constructor(contentRendering) {
        this.contentRendering = contentRendering;
    }

    static getSubscribedEvents() {
        return {
            'element.data.changed': 'renderContent',
            'structure.changed': 'renderContent',
            'element.config.sync': 'renderContent',
            'element.data.replace': 'renderContent',
        };
    }

    renderContent() {
        this.contentRendering.render();
    }
}
