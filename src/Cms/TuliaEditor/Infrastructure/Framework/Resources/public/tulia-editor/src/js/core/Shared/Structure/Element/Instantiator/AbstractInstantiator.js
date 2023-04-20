export default class AbstractInstantiator {
    instances = {};

    constructor(type, messenger, elementConfigStoreRegistry, elementDataStoreFactory) {
        this.type = type;
        this.messenger = messenger;
        this.elementConfigStoreRegistry = elementConfigStoreRegistry;
        this.elementDataStoreFactory = elementDataStoreFactory;
    }

    createInstance(elementId, args, segment) {
        // To be implemented in instantiators
    }

    manager(props) {
        return this.instance('manager', props);
    }

    render (props) {
        return this.instance('render', props);
    }

    editor (props) {
        return this.instance('editor', props);
    }

    instance (segment, props) {
        const elementId = AbstractInstantiator.getElementIdByType(props, this.type);
        const instanceKey = `${this.type}-${segment}-${elementId}`;

        if (this.instances[instanceKey]) {
            return this.instances[instanceKey];
        }

        const args = [
            elementId,
            this.type,
            this.messenger,
            this.elementConfigStoreRegistry,
            this.elementDataStoreFactory,
        ];

        return this.instances[instanceKey] = this.createInstance(elementId, args, segment);
    }

    static getElementIdByType (props, type) {
        if (typeof props === 'string' || props instanceof String) {
            return props;
        }

        switch (type) {
            case 'block': return props.block.id;
            case 'column': return props.column.id;
            case 'row': return props.row.id;
            case 'section': return props.section.id;
        }
    }
}
