import { v4 } from "uuid";

export default class Sections {
    constructor(structure, messenger, selectionUseCase) {
        this.structure = structure;
        this.messenger = messenger;
        this.selectionUseCase = selectionUseCase;
    }

    newOne() {
        const id = v4();
        this.structure.appendSection({ id });
        this.selectionUseCase.select(id, 'section');
        this.update();
    }

    newOneAfter(afterId) {
        const id = v4();
        this.structure.appendSectionAfter({ id }, afterId);
        this.selectionUseCase.select(id, 'section');
        this.update();
    }

    remove(id) {
        this.structure.removeSection(id);
        this.update();
    }

    update() {
        this.messenger.send('admin.structure.changed', {
            structure: this.structure.export,
        });
    }
}
