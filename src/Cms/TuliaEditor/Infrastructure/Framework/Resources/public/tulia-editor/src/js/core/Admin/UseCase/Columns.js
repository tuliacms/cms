import { v4 } from "uuid";

export default class Columns {
    constructor(structureStore, selectionUseCase, structure) {
        this.structureStore = structureStore;
        this.selectionUseCase = selectionUseCase;
        this.structure = structure;
    }

    newOne(rowId) {
        const id = v4();
        this.structureStore.appendColumn({ id }, rowId);
        this.selectionUseCase.select(id, 'column');
        this.structure.update();
    }

    newBefore(before) {
        const id = v4();
        this.structureStore.appendColumnBefore({ id }, before);
        this.selectionUseCase.select(id, 'column');
        this.structure.update();
    }

    newAfter(after) {
        const id = v4();
        this.structureStore.appendColumnAfter({ id }, after);
        this.selectionUseCase.select(id, 'column');
        this.structure.update();
    }

    remove(id) {
        this.structureStore.removeColumn(id);
        this.structure.update();
    }
}
