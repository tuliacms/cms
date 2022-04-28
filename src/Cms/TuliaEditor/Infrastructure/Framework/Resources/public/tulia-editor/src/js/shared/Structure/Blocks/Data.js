const { reactive, toRaw, watch } = require('vue');
const _ = require('lodash');

export default class Data {
    data;
    blockId;
    messenger;
    owner;
    lastUpdateFromOutside = false;
    onChangeCallable;

    constructor (blockId, owner, dataProperty, messenger) {
        this.blockId = blockId;
        this.owner = owner;
        this.messenger = messenger;

        this.data = reactive(dataProperty);

        this.propagateChangesInThisInstance();
        this.watchForChangesInOtherInstance();
    }

    get reactiveData () {
        return this.data;
    }

    onChange (callable) {
        this.onChangeCallable = callable;
    }

    propagateChangesInThisInstance () {
        watch(this.data, async (newData, oldData) => {
            if (this.lastUpdateFromOutside) {
                return;
            }

            this.messenger.execute('structure.block.data.update', {
                owner: this.owner,
                data: toRaw(newData),
                blockId: this.blockId,
            });
        });
    }

    watchForChangesInOtherInstance () {
        this.messenger.operation('structure.block.data.update', (data, success, fail) => {
            // Catch only operations for the same block in all windows
            if (data.blockId === this.blockId && data.owner !== this.owner) {
                this.handleDataUpdate(data);

                if (this.onChangeCallable) {
                    this.onChangeCallable();
                }
            }

            success();
        });
    }

    handleDataUpdate (newData) {
        if (_.isEqual(newData.data, toRaw(this.data)) === false) {
            for (let i in newData.data) {
                this.lastUpdateFromOutside = true;
                this.data[i] = newData.data[i];
            }
            this.lastUpdateFromOutside = false;
        }
    }
}
