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

        /**
         * @todo Implement default values here, and remove loops from
         * TuliaEditor/Infrastructure/Framework/Resources/public/tulia-editor/src/js/core/Admin/Structure/Structure.js:72
         */
        switch (type) {
            case 'section':
                store = this.factory.forSection(id, {})(); break;
            case 'row':
                store = this.factory.forRow(id, {})(); break;
            case 'column':
                store = this.factory.forColumn(id, {})(); break;
            case 'block':
                store = this.factory.forBlock(id, {})(); break;
            default:
                return;
        }

        return store;
    }
}
