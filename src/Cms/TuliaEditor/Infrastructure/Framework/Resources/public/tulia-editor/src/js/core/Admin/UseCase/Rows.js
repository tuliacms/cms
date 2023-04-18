import { v4 } from "uuid";

export default class Rows {
    constructor(structureStore, selectionUseCase, structure) {
        this.structureStore = structureStore;
        this.selectionUseCase = selectionUseCase;
        this.structure = structure;
    }

    newOne(sectionId) {
        const id = v4();
        this.structureStore.appendRow({ id }, sectionId);
        this.selectionUseCase.select(id, 'row');
        this.structure.update();
    }

    remove(id) {
        this.structureStore.removeRow(id);
        this.structure.update();
    }
}
