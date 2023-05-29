export default class View {
    constructor(eventBus) {
        this.eventBus = eventBus;
    }

    render() {
        this.eventBus.dispatch('editor.view.ready');
    };
}
