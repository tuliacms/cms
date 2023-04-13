export default class AbstractSegment {
    constructor(id, type, elementConfigStoreRegistry, elementDataStoreFactory) {
        this.id = id;
        this.type = type;
        this.elementConfigStoreRegistry = elementConfigStoreRegistry;
        this.elementDataStoreFactory = elementDataStoreFactory;
    }

    get config() {
        return this.elementConfigStoreRegistry.get(this.id, this.type);
    }

    get data() {
        return this.elementDataStoreFactory.get(this.id, this.type);
    }
}
