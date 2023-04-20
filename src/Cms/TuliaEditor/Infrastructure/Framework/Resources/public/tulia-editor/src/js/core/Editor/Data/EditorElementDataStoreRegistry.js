import ElementDataStoreRegistry from "core/Shared/Structure/Element/Data/ElementDataStoreRegistry";

export default class EditorElementDataStoreRegistry extends ElementDataStoreRegistry {
    constructor(factory, dataSynchronizer) {
        super(factory);
        this.dataSynchronizer = dataSynchronizer;
    }

    create(id, type) {
        const store = super.create(id, type);

        this.dataSynchronizer.sync(id, type, store);

        return store;
    }
}
