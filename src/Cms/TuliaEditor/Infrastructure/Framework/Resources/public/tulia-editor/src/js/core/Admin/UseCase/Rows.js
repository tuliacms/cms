import { v4 } from "uuid";

export default class Rows {
    constructor(structure, messenger, selectionUseCase) {
        this.structure = structure;
        this.messenger = messenger;
        this.selectionUseCase = selectionUseCase;
    }

    newOne(sectionId) {
        const id = v4();
        this.structure.appendRow({ id }, sectionId);
        this.selectionUseCase.select(id, 'row');
        this.update();
    }

    remove(id) {
        this.structure.removeRow(id);
        this.update();
    }

    update() {
        this.messenger.send('admin.structure.changed', {
            structure: this.structure.export,
        });
    }
}
