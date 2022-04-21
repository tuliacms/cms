const { reactive, toRaw, watch } = require('vue');

export default class Data {
    data;
    blockId;
    messenger;
    owner;
    version = 0;
    lastUpdateFromOutside = false;

    constructor (blockId, owner, props, messenger) {
        this.blockId = blockId;
        this.owner = owner;
        this.messenger = messenger;

        this.data = reactive(props.data);

        this.propagateChangesInThisInstance();
        this.watchForChangesInOtherInstance();
    }

    get reactiveData () {
        return this.data;
    }

    propagateChangesInThisInstance () {
        watch(this.data, async (newData, oldData) => {
            if (this.lastUpdateFromOutside) {
                return;
            }

            this.version++;

            this.messenger.execute('structure.block.data.update', {
                owner: this.owner,
                data: toRaw(newData),
                blockId: this.blockId,
                version: this.version,
            });
        });
    }

    watchForChangesInOtherInstance () {
        this.messenger.operation('structure.block.data.update', (data, success, fail) => {
            // Catch only operations for the same block in all windows
            if (data.blockId === this.blockId && data.owner !== this.owner) {
                this.handleDataUpdate(data);
            }

            success();
        });
    }

    handleDataUpdate (newData) {
        if (newData.version > this.version) {
            for (let i in newData.data) {
                this.lastUpdateFromOutside = true;
                this.data[i] = newData.data[i];
            }
            this.lastUpdateFromOutside = false;
            this.version = newData.version;
        }
    }
}
