import ElementConfigStoreRegistry from "core/Shared/Structure/Element/Config/ElementConfigStoreRegistry";

export default class AdminElementConfigStoreRegistry extends ElementConfigStoreRegistry {
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
