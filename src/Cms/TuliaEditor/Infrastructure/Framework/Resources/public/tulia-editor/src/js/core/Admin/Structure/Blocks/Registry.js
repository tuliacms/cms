export default class Registry {
    blocks;

    constructor (blocks) {
        this.blocks = blocks;
    }

    get (code) {
        return this.blocks[code];
    }
}
