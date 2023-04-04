import { v4 } from "uuid";

export default class Columns {
    constructor(structure, messenger, selectionUseCase) {
        this.structure = structure;
        this.messenger = messenger;
        this.selectionUseCase = selectionUseCase;
    }

    newOne(rowId) {
        const id = v4();
        this.structure.appendColumn({ id }, rowId);
        this.selectionUseCase.select(id, 'column');
        this.update();
    }

    newBefore(before) {
        const id = v4();
        this.structure.appendColumnBefore({ id }, before);
        this.selectionUseCase.select(id, 'column');
        this.update();
    }

    newAfter(after) {
        const id = v4();
        this.structure.appendColumnAfter({ id }, after);
        this.selectionUseCase.select(id, 'column');
        this.update();
    }

    remove(id) {
        this.structure.removeColumn(id);
        this.update();
    }

    update() {
        this.messenger.send('admin.structure.changed', {
            structure: this.structure.export,
        });
    }
}
