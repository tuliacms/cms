export default class ContextmenuSubscriber {
    constructor(contextmenu) {
        this.contextmenu = contextmenu;
    }

    static getSubscribedEvents() {
        return {
            'draggable.start': 'hide',
        };
    }

    hide() {
        this.contextmenu.hide();
    }
}
