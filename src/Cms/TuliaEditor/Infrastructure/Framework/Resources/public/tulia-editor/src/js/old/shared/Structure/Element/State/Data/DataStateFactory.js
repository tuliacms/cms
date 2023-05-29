import { defineStore } from 'pinia';

export default class {
    constructor(blocksRegistry) {
        this.blocksRegistry = blocksRegistry;
    }

    forBlock(id, type, currentData) {
        dump(id, type, currentData);
        return '';
        return defineStore(`block.${id}`, () => {
            const data = {};

            return data;
        });
    }
}
