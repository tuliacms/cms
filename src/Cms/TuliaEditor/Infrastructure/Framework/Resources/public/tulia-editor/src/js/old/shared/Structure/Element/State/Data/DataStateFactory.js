import { defineStore } from 'pinia';

export default class {
    constructor(blocksRegistry) {
        this.blocksRegistry = blocksRegistry;
    }

    forBlock(id, type, currentData) {
        return defineStore(`block.${id}`, () => {
            const data = {};

            return data;
        });
    }
}
