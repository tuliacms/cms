import { reactive } from "vue";

export default class BlocksPicker {
    constructor (blocks, modals) {
        this.blocks = blocks;
        this.modals = modals;
        this.config = reactive({ columnId: null });
    }

    new() {
        this.modals.open('tued-block-picker-modal');
    }

    newAt(columnId) {
        this.config.columnId = columnId;
        this.modals.open('tued-block-picker-modal');
    }

    close() {
        this.config.columnId = null;
        this.modals.close('tued-block-picker-modal');
    }

    isOpened() {
        return this.modals.isOpened('tued-block-picker-modal');
    }

    select(code) {
        this.blocks.newBlock(code, this.config.columnId);
        this.close();
    }
}
