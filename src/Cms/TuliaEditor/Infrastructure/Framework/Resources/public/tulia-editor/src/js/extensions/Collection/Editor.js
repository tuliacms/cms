const _ = require('lodash');

export default class Collection {
    _collection;
    prototype;

    constructor (collection, prototype) {
        this._assertThatAllElementsContainsId(collection);

        this._collection = collection;
        this.prototype = prototype;
    }

    get collection () {
        return this._collection;
    }

    remove (element) {
        for (let i in this._collection) {
            if (this._collection[i].id === element.id) {
                this._collection.splice(i, 1);
            }
        }
    }

    add () {
        let element = _.assign({}, this.prototype);
        element.id = this._generateId();

        this._collection.push(element);

        return this._collection.length - 1;
    }

    moveBackward (element) {
        let index = this._findIndex(element);

        // Current element's index does not exists = try to move element that's not exists
        if (index === undefined) {
            return;
        }

        // Cannot move forward very first element of collection
        if (index - 1 < 0) {
            return;
        }

        this.moveToIndex(element, index, index - 1);

        return index - 1;
    }

    moveForward (element) {
        let index = this._findIndex(element);

        // Current element's index does not exists = try to move element that's not exists
        if (index === undefined) {
            return;
        }

        // Cannot move forward the last element of collection
        if (index + 1 >= this._collection.length) {
            return;
        }

        this.moveToIndex(element, index, index + 1);

        return index + 1;
    }

    moveToIndex (element, oldIndex, newIndex) {
        this._collection.splice(oldIndex, 1);
        this._collection.splice(newIndex, 0, element);
    }

    _findIndex (element) {
        for (let i in this._collection) {
            if (this._collection[i].id === element.id) {
                return parseInt(i);
            }
        }
    }

    _generateId () {
        return `tued-collection-${_.uniqueId()}`;
    }

    _assertThatAllElementsContainsId (collection) {
        for (let i in collection) {
            if (!collection[i].hasOwnProperty('id')) {
                console.log(collection[i]);
                throw new Error(`All elements of Collection must contains "id" property.`);
            } else if (!collection[i].id) {
                console.log(collection[i]);
                throw new Error(`All elements of Collection must contains filled "id" property with unique value across collection.`);
            }
        }
    }
}
