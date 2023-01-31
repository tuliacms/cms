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

        // In 'render' we don't change anything, so we don't need to update other segments
        if (this.segment !== 'render') {
            this.propagateChangesInThisInstance();
        }

        this.watchForChangesInOtherInstance();
    }

    get reactiveData () {
        return this.data;
    }

    onChange (callable) {
        this.onChangeCallable = callable;
    }

    propagateChangesInThisInstance () {
        watch(this.data, async (newData) => {
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
        this.lastUpdateFromOutside = true;

        if (_.isEqual(newData.data, toRaw(this.data)) === false) {
            this.mergeObjectRecursive(newData.data, this.data);
        }

        this.lastUpdateFromOutside = false;
    }

    mergeObjectRecursive (from, to) {
        const fromKeys = Object.keys(from);
        const toKeys = Object.keys(to);

        const toAdd = fromKeys.filter(x => !toKeys.includes(x));
        const toRemove = toKeys.filter(x => !fromKeys.includes(x));
        const toUpdate = fromKeys.filter(x => toKeys.includes(x));

        for (let i in toUpdate) {
            this.mergeValue(from, to, toUpdate[i]);
        }

        for (let i in toRemove) {
            delete to[toUpdate[i]];
        }

        for (let i in toAdd) {
            const key = toAdd[i];
            to[key] = from[key];
        }
    }

    mergeValue (from, to, key) {
        switch (this.getType(from[key])) {
            case 'object':
                if (to[key] === undefined) {
                    to[key] = {};
                }

                this.mergeObjectRecursive(from[key], to[key]);
                break;
            case 'array':
                if (to[key] === undefined) {
                    to[key] = [];
                }

                this.mergeArrayRecursive(from[key], to[key]);
                break;
            default:
                to[key] = from[key];
        }
    }

    mergeArrayRecursive (from, to) {
        if (from.length === 0) {
            to.splice(0, to.length);
            return;
        }

        if (from.length === to.length) {
            for (let i in from) {
                this.mergeValue(from, to, i);
            }
        }

        if (from.length > to.length) {
            for (let i in from) {
                if (typeof to[i] === 'undefined') {
                    to.push(from[i]);
                } else {
                    this.mergeValue(from, to, i);
                }
            }
        }

        if (from.length < to.length) {
            for (let i in to) {
                if (typeof from[i] === 'undefined') {
                    delete to[i];
                } else {
                    this.mergeValue(from, to, i);
                }
            }
        }
    }

    getType (item) {
        if (item === null) {
            return 'scalar';
        }
        if (Array.isArray(item)) {
            return 'array';
        }
        if (typeof item === 'object') {
            return 'object';
        }

        return 'scalar';
    }
}
