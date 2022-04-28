export default class BlocksPicker {
    config;
    structure;
    modals;
    blocksRegistry;

    constructor (config, blocksRegistry, structure, modals) {
        this.blocksRegistry = blocksRegistry;
        this.config = config;
        this.structure = structure;
        this.modals = modals;
    }

    newAt (columnId) {
        this.config.columnId = columnId;
        this.modals.open('tued-block-picker-modal');
    }

    close () {
        this.config.columnId = null;
        this.modals.close('tued-block-picker-modal');
    }

    isOpened () {
        return this.modals.isOpened('tued-block-picker-modal');
    }

    select (code) {
        this.structure.newBlock(code, this.config.columnId, this.blocksRegistry.get(code).defaults);
        this.close();
    }
}
