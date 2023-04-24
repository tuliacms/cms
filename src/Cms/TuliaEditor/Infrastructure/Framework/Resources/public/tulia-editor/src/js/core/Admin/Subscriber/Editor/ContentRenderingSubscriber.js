export default class ContentRenderingSubscriber {
    constructor(messenger, structureRenderer) {
        this.messenger = messenger;
        this.structureRenderer = structureRenderer;
    }

    static getSubscribedEvents() {
        return {
            'admin.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('editor.content.rendered', (data) => self.storeContent(data));
    }

    storeContent(content) {
        this.structureRenderer.setContent(content);
    }
}
