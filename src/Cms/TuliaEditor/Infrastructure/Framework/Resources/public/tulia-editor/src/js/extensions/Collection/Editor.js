const _ = require('lodash');

export default class Collection {
    collection;
    prototype;

    constructor (collection, prototype) {
        this.collection = this.createIdFieldFor(collection);
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
        element.id = this.generateId();

        this.collection.push(element);
    }

    generateId () {
        return `tued-collection-${_.uniqueId()}`;
    }

    createIdFieldFor (collection) {
        for (let i in collection) {
            collection[i].id = this.generateId();
        }
        return collection;
    }
}
