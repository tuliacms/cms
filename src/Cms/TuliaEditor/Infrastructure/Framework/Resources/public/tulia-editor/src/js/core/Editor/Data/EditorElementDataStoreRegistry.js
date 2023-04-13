import ElementDataStoreRegistry from "core/Shared/Structure/Element/Data/ElementDataStoreRegistry";

export default class EditorElementDataStoreRegistry extends ElementDataStoreRegistry {
    constructor(factory, configSynchronizer) {
        super(factory);
        this.configSynchronizer = configSynchronizer;
    }

    create(id, type) {
        const store = super.create(id, type);

        this.configSynchronizer.sync(id, type, store);

        return store;
    }
}
