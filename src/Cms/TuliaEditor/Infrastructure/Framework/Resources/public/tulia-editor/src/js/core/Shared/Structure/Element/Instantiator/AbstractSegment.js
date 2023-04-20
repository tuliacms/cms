export default class AbstractSegment {
    constructor(id, type, messenger, elementConfigStoreRegistry, elementDataStoreFactory) {
        this.id = id;
        this.type = type;
        this.messenger = messenger;
        this.elementConfigStoreRegistry = elementConfigStoreRegistry;
        this.elementDataStoreFactory = elementDataStoreFactory;
    }

    get config() {
        return this.elementConfigStoreRegistry.get(this.id, this.type);
    }

    get data() {
        return this.elementDataStoreFactory.get(this.id, this.type);
    }

    send (operation, data) {
        this.messenger.send(this.generatePrefix(operation), data);
    }

    receive (operation, callable) {
        this.messenger.receive(this.generatePrefix(operation), callable);
    }

    generatePrefix (operation) {
        return `elm.operation.${this.type}.${this.id}.${operation}`;
    }
}
