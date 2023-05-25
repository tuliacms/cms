export default class Instantiator {
    instances = {};

    constructor(
        messenger,
        elementConfigStoreRegistry,
        elementDataStoreFactory,
        structureStore,
        blockInstantiator,
        columnInstantiator,
        rowInstantiator,
        sectionInstantiator,
    ) {
        this.messenger = messenger;
        this.elementConfigStoreRegistry = elementConfigStoreRegistry;
        this.elementDataStoreFactory = elementDataStoreFactory;
        this.structureStore = structureStore;

        this.blockInstantiator = blockInstantiator;
        this.columnInstantiator = columnInstantiator;
        this.rowInstantiator = rowInstantiator;
        this.sectionInstantiator = sectionInstantiator;
    }

    instance(identity) {
        this.validateIdentity(identity);

        const instanceKey = `${identity.type}-${identity.id}`;

        if (this.instances[instanceKey]) {
            return this.instances[instanceKey];
        }

        const args = [
            identity.id,
            identity.type,
            this.messenger,
            this.elementConfigStoreRegistry,
            this.elementDataStoreFactory,
            (id) => {
                return this.instance(this.structureStore.findParent(id));
            }
        ];

        switch (identity.type) {
            case 'block': return this.instances[instanceKey] = this.blockInstantiator.createInstance(identity.id, args);
            case 'column': return this.instances[instanceKey] = this.columnInstantiator.createInstance(identity.id, args);
            case 'row': return this.instances[instanceKey] = this.rowInstantiator.createInstance(identity.id, args);
            case 'section': return this.instances[instanceKey] = this.sectionInstantiator.createInstance(identity.id, args);
        }

        throw new Error('Unsupported element type.');
    }

    validateIdentity(identity) {
        if (identity instanceof String) {
            throw new Error('Identity must be object with "id" and "type" properties.');
        }
    }
}
