const BlockEditor = require('shared/Structure/Blocks/Editor/BlockEditor.js').default;
const BlockManager = require('shared/Structure/Blocks/Editor/BlockManager.js').default;
const BlockRender = require('shared/Structure/Blocks/Editor/BlockRender.js').default;

export default class Blocks {
    hooks;
    blocksOptions;

    constructor (hooks, blocksOptions) {
        this.hooks = hooks;
        this.blocksOptions = blocksOptions;
    }

    editor (code, props, data) {
        return new BlockEditor(code, props, data, this.blocksOptions[code] ?? {}, this.hooks);
    }

    manager (code, props, data) {
        return new BlockManager(code, props, data, this.blocksOptions[code] ?? {}, this.hooks);
    }

    render (code, props, data) {
        return new BlockRender(code, props, data, this.blocksOptions[code] ?? {}, this.hooks);
    }
}
