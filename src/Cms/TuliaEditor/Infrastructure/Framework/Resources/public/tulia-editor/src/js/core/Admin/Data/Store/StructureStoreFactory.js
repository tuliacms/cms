import { useStructureStore } from "core/Admin/Data/Store/Structure";

export default class StructureStoreFactory {
    constructor(options) {
        this.options = options;
    }

    factory() {
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
            rows: [
                {id: "7ac6f37f-cc0c-4c9d-9c2b-bed9ef4e1dd0", parent: "3aaab68f-73a8-4871-bf3e-6e8698eed744"},
                {id: "392f96b7-ebcd-451c-a0c6-9a55233f2aad", parent: "70d5581b-d415-4e00-a509-650eefc50b52"},
                {id: "fcc99a1f-5b98-49cc-b69b-9c686e4c133d", parent: "08023e72-0934-402d-8dcd-2f1128bdb1dc"},
            ],
        });

        return store;
    }

    fill(store, structure) {
        for (let s in structure.sections) {
            store.appendSection(structure.sections[s]);
        }
        for (let s in structure.rows) {
            store.appendRow(structure.rows[s], structure.rows[s].parent);
        }
    }
}
