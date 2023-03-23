export default class StructureStoreFactory {
    constructor(options) {
        this.options = options;
    }

    factory(useStructureStore) {
        const store = useStructureStore();

        //this.fill(store, this.options.structure.source);

        this.fill(store, {
            sections: [
                {id: "3aaab68f-73a8-4871-bf3e-6e8698eed744"},
                {id: "70d5581b-d415-4e00-a509-650eefc50b52"},
                {id: "08023e72-0934-402d-8dcd-2f1128bdb1dc"},
                {id: "79322024-843c-48f9-8bb0-e6a21f1f5d89"},
                {id: "427d2344-79d5-4b1d-a098-916514619d2f"},
            ],
        });

        return store;
    }

    fill(store, structure) {
        for (let s in structure.sections) {
            store.appendSection(structure.sections[s]);
        }
    }
}
