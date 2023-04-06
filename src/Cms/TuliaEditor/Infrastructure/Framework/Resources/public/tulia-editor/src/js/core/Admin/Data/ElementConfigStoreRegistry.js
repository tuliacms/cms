export default class ElementConfigStoreRegistry {
    constructor(factory, configSynchronizer) {
        this.factory = factory;
        this.configSynchronizer = configSynchronizer;
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
                store = this.factory.forSection(id, {a:1})();
                break;
            default:
                return;
        }

        this.configSynchronizer.sync(id, type, store);

        return store;
    }
}
