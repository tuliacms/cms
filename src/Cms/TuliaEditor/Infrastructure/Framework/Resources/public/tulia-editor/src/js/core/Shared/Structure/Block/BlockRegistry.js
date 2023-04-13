export default class BlockRegistry {
    blocks;

    constructor(blocks) {
        this.blocks = blocks;
    }

    get(code) {
        return this.blocks[code];
    }
}
