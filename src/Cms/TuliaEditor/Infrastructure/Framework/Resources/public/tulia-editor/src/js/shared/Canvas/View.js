export default class View {
    messenger;

    constructor (messenger) {
        this.messenger = messenger;

        this.messenger.on('structure.element.updated', () => this.updated());
    }

    updated () {
        this.messenger.notify('canvas.view.updated');
    }
}
