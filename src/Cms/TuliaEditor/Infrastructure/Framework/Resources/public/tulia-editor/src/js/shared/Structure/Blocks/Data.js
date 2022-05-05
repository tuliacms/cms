const { reactive, toRaw, watch } = require('vue');
const _ = require('lodash');

export default class Data {
    data;
    elementId;
    messenger;
    prefix;
    owner;
    lastUpdateFromOutside = false;
    onChangeCallable;

    constructor (elementId, owner, dataProperty, messenger, prefix) {
        this.elementId = elementId;
        this.owner = owner;
        this.messenger = messenger;
        this.prefix = prefix;

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

            this.messenger.execute(`structure.${this.prefix}.data.update`, {
                owner: this.owner,
                data: toRaw(newData),
                elementId: this.elementId,
            });
        });
    }

    watchForChangesInOtherInstance () {
        this.messenger.operation(`structure.${this.prefix}.data.update`, (data, success, fail) => {
            // Catch only operations for the same block in all windows
            if (data.elementId === this.elementId && data.owner !== this.owner) {
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
