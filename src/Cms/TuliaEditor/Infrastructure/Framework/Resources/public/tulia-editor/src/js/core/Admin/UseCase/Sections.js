const { v4 } = require('uuid');

export default class Sections {
    constructor(structure) {
        this.structure = structure;
    }

    newOne() {
        this.structure.appendSection({
            id: v4(),
        });
    }
}
