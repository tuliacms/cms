import { v4 } from "uuid";

export default class Sections {
    constructor(structureStore, selectionUseCase, structure) {
        this.structureStore = structureStore;
        this.selectionUseCase = selectionUseCase;
        this.structure = structure;
    }

    newOne() {
        const id = v4();
        this.structureStore.appendSection({ id });
        this.selectionUseCase.select(id, 'section');
        this.structure.update();
    }

    newOneAfter(afterId) {
        const id = v4();
        this.structureStore.appendSectionAfter({ id }, afterId);
        this.selectionUseCase.select(id, 'section');
        this.structure.update();
    }

    remove(id) {
        this.structureStore.removeSection(id);
        this.structure.update();
    }
}
