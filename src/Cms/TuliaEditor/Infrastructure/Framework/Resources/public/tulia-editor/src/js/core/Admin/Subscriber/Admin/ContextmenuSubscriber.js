export default class ContextmenuSubscriber {
    constructor(contextmenu) {
        this.contextmenu = contextmenu;
    }

    hide() {
        this.contextmenu.hide();
    }
}
