export default class AbstractInstance {
    constructor(
        id,
        type,
        messenger,
        elementConfigStoreRegistry,
        elementDataStoreFactory,
        parentResolver,
    ) {
        this.id = id;
        this.type = type;
        this.messenger = messenger;
        this.elementConfigStoreRegistry = elementConfigStoreRegistry;
        this.elementDataStoreFactory = elementDataStoreFactory;
        this.parentResolver = parentResolver;
    }

    get config() {
        return this.elementConfigStoreRegistry.get(this.id, this.type);
    }

    get data() {
        return this.elementDataStoreFactory.get(this.id, this.type);
    }

    get parent() {
        return this.parentResolver(this.id);
    }

    send(operation, data) {
        this.messenger.send(this.generateOperationPrefix(operation), data);
    }

    receive(operation, callable) {
        this.messenger.receive(this.generateOperationPrefix(operation), callable);
    }

    generateOperationPrefix(operation) {
        return `elm.operation.${this.type}.${this.id}.${operation}`;
    }
}
