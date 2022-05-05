const { reactive, toRaw, watch } = require('vue');
const _ = require('lodash');

export default class Data {
    data;
    elementId;
    messenger;
    elementType;
    segment;
    lastUpdateFromOutside = false;
    onChangeCallable;

    constructor (elementId, elementType, segment, data, messenger) {
        this.elementId = elementId;
        this.elementType = elementType;
        this.segment = segment;
        this.messenger = messenger;

        this.data = reactive(data);

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

            this.messenger.execute(`structure.element.data.update`, {
                segment: this.segment,
                data: toRaw(newData),
                elementId: this.elementId,
                elementType: this.elementType,
            });
        });
    }

    watchForChangesInOtherInstance () {
        this.messenger.operation(`structure.element.data.update`, (data, success, fail) => {
            // Catch only operations for the same block in all windows
            if (
                data.elementId === this.elementId
                && data.elementType === this.elementType
                && data.segment !== this.segment
            ) {
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
