import EventTransformer from "core/Shared/ContextMenu/EventTransformer";

export default class Contextmenu {
    itemsCollection = {};

    constructor(store) {
        this.store = store;
    }

    setEditorOffsetProvider(provider) {
        this.editorOffsetProvider = provider;
    }

    open(event) {
        if (this.isTextSelected()) {
            return;
        }

        const result = EventTransformer.transformPointerEvent(event);
        const collection = this.collectItems(result.targets);

        if (!collection.total) {
            return;
        }

        event.preventDefault();

        this.store.open(collection, result.position);
    }

    openFromEditor(targets, position) {
        const collection = this.collectItems(targets);

        if (!collection.total) {
            return;
        }

        const offset = this.editorOffsetProvider();

        position.x = position.x + offset.left;
        position.y = position.y + offset.top;

        this.store.open(collection, position);
    }

    hide() {
        this.store.hide();
    }

    isTextSelected() {
        const selection = window.getSelection();
        return selection && selection.type === 'Range';
    };

    register(type, elementId, data) {
        return JSON.stringify({
            type: type,
            elementId: elementId,
            data: data,
        });
    }

    items(id, type, callback) {
        if (false === this.itemsCollection.hasOwnProperty(type)) {
            this.itemsCollection[type] = {};
        }

        this.itemsCollection[type][id] = callback;
    }

    collectItems(targets) {
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

    prepareCallbacks(items, elementId) {
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
