export default class AbstractSegment {
    constructor(id, type, elementConfigStoreRegistry) {
        this.id = id;
        this.type = type;
        this.elementConfigStoreRegistry = elementConfigStoreRegistry;
    }

    get config() {
        return this.elementConfigStoreRegistry.get(this.id, this.type);
    }
}
