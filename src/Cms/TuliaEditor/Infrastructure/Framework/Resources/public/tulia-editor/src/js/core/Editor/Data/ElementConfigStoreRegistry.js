export default class ElementConfigStoreRegistry {
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
            case 'section':
                store = this.factory.forSection(id, {a:1})(); break;
            case 'column':
                store = this.factory.forColumn(id, {a:1})(); break;
            default:
                return;
        }

        return store;
    }
}
