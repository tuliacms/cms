export default class ElementDataStoreRegistry {
    constructor(factory) {
        this.factory = factory;
        this.stores = {};
    }

    get(id, type) {
        if (!this.stores[type]) {
            this.stores[type] = {};
        }

        if (!this.stores[type][id]) {
            this.stores[type][id] = this.create(id, type);
        }

        return this.stores[type][id];
    }

    create(id, type) {
        let store = null;

        switch (type) {
            case 'block':
                store = this.factory.forBlock(id, {})(); break;
            default:
                return;
        }

        return store;
    }
}
