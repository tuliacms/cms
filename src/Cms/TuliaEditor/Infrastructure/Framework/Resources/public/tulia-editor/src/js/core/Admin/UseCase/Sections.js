const { v4 } = require('uuid');

export default class Sections {
    constructor(structure, messenger) {
        this.structure = structure;
        this.messenger = messenger;
    }

    newOne() {
        this.structure.appendSection({
            id: v4(),
        });
        this.update();
    }

    update() {
        this.messenger.send('admin.structure.changed', {
            structure: this.structure.export,
        });
    }
}
