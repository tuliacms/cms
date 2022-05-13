const _ = require('lodash');

export default class Collection {
    collection;
    prototype;

    constructor (collection, prototype) {
        this._assertThatAllElementsContainsId(collection);

        this.collection = collection;
        this.prototype = prototype;
    }

    remove (element) {
        for (let i in this.collection) {
            if (this.collection[i].id === element.id) {
                this.collection.splice(i, 1);
            }
        }
    }

    add () {
        let element = _.assign({}, this.prototype);
        element.id = this._generateId();

        this.collection.push(element);
    }

    moveBackward (element) {
        let index = this._findIndex(element);

        if (!index) {
            return;
        }

        this.moveToIndex(element, index, index - 1);
    }

    moveForward (element) {
        let index = this._findIndex(element);

        if (!index) {
            return;
        }

        this.moveToIndex(element, index, index + 1);
    }

    moveToIndex (element, oldIndex, newIndex) {
        this.collection.splice(oldIndex, 1);
        this.collection.splice(newIndex, 0, element);
    }

    _findIndex (element) {
        for (let i in this.collection) {
            if (this.collection[i].id === element.id) {
                return i;
            }
        }
    }

    _generateId () {
        return `tued-collection-${_.uniqueId()}`;
    }

    _assertThatAllElementsContainsId (collection) {
        for (let i in collection) {
            if (!collection[i].id) {
                console.log(collection[i]);
                throw new Error('All elements of Collection must contains ID property.');
            }
        }
    }
}
