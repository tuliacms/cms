const _ = require('lodash');

export default class Collection {
    block;
    property;
    prototype;

    constructor (block, property, prototype) {
        this._assertThatAllElementsContainsId(block.data[property]);

        this.block = block;
        this.property = property;
        this.prototype = prototype;
    }

    get collection () {
        return this.block.data[this.property];
    }

    remove (element) {
        for (let i in this.block.data[this.property]) {
            if (this.block.data[this.property][i].id === element.id) {
                this.block.data[this.property].splice(i, 1);
            }
        }
    }

    add () {
        let element = _.assign({}, this.prototype);
        element.id = this._generateId();

        this.block.data[this.property].push(element);

        return this.block.data[this.property].length - 1;
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
        if (index + 1 >= this.block.data[this.property].length) {
            return;
        }

        this.moveToIndex(element, index, index + 1);

        return index + 1;
    }

    moveToIndex (element, oldIndex, newIndex) {
        this.block.data[this.property].splice(oldIndex, 1);
        this.block.data[this.property].splice(newIndex, 0, element);
    }

    _findIndex (element) {
        for (let i in this.block.data[this.property]) {
            if (this.block.data[this.property][i].id === element.id) {
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
