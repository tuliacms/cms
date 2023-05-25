export default class Inspector {
    constructor(messenger) {
        this.messenger = messenger;
    }

    inspect(id, type) {
        this.messenger.send('structure.element.inspect', { id, type });
    }
}
