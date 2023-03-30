import { v4 } from "uuid";

export default class Rows {
    constructor(structure, messenger) {
        this.structure = structure;
        this.messenger = messenger;
    }

    newOne(sectionId) {
        this.structure.appendRow({ id: v4() }, sectionId);
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
