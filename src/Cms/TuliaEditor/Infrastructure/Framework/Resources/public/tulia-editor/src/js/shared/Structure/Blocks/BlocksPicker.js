export default class BlocksPicker {
    config;
    structureManipulator;
    modals;
    blocksRegistry;

    constructor (config, blocksRegistry, structureManipulator, modals) {
        this.blocksRegistry = blocksRegistry;
        this.config = config;
        this.structureManipulator = structureManipulator;
        this.modals = modals;
    }

    new () {
        this.modals.open('tued-block-picker-modal');
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
        this.structureManipulator.newBlock(code, this.config.columnId, this.blocksRegistry.get(code).defaults);
        this.close();
    }
}
