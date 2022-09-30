export default class {
    source;
    messenger;
    itemsCollection = {};

    constructor (source, messenger) {
        this.source = source;
        this.messenger = messenger;
    }

    register (type, elementId, data) {
        return JSON.stringify({
            type: type,
            elementId: elementId,
            data: data,
        });
    }

    items (id, type, callback) {
        if (this.source === 'editor') {
            throw new Error('Cannot register Contextmenu items in Editor. Please use a Manager section to do this.');
        }

        if (false === this.itemsCollection.hasOwnProperty(type)) {
            this.itemsCollection[type] = {};
        }

        this.itemsCollection[type][id] = callback;
    }

    collectItems (targets) {
        let items = [];

        for (let t in targets) {
            if (false === this.itemsCollection.hasOwnProperty(targets[t].type)) {
                continue;
            }

            for (let i in this.itemsCollection[targets[t].type]) {
                let targetItems = this.itemsCollection[targets[t].type][i].apply(null, [targets[t].elementId]);

                items = items.concat(this.prepareCallbacks(targetItems, targets[t].elementId));
            }
        }

        let groups = {
            total: 0,
            collection: {
                custom: {label: null, items: []},
                block: {label: 'block', items: []},
                column: {label: 'column', items: []},
                row: {label: 'row', items: []},
                section: {label: 'section', items: []},
            },
        };

        for (let i in items) {
            if (false === items[i].hasOwnProperty('icon')) {
                items[i].icon = null;
            }
            if (false === items[i].hasOwnProperty('classname')) {
                items[i].classname = '';
            }
            if (false === items[i].hasOwnProperty('label')) {
                items[i].label = '--item without label--';
            }
            if (false === items[i].hasOwnProperty('group')) {
                items[i].group = 'custom';
            }

            groups.collection[items[i].group].items.push(items[i]);
            groups.total++;
        }

        for (let i in groups.collection) {
            if (groups.collection[i].items.length === 0) {
                delete groups.collection[i];
            }
        }

        return groups;
    }

    prepareCallbacks (items, elementId) {
        for (let i in items) {
            if (false === items[i].hasOwnProperty('onClick')) {
                items[i].onClick = () => {};
            }

            items[i].callback = () => {
                items[i].onClick(elementId);
            };
        }

        return items;
    }
}
