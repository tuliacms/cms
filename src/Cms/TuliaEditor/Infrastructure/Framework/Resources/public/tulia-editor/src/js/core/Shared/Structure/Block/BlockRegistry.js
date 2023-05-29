export default class BlockRegistry {
    blocks;

    constructor(blocks) {
        this.blocks = blocks;
    }

    get(code) {
        return this.blocks[code];
    }

    hasComponent(code, segment) {
        const block = this.get(code);

        return block && block.hasOwnProperty(segment);
    }

    getComponentName(code, segment) {
        return `block-${code}-${segment}`;
    }
}
