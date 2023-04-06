export default class AbstractSegment {
    constructor(id, type, elementStoreRegistry) {
        this.id = id;
        this.type = type;
        this.elementStoreRegistry = elementStoreRegistry;
    }

    get config() {
        return this.elementStoreRegistry.get(this.id, this.type);
    }
}
