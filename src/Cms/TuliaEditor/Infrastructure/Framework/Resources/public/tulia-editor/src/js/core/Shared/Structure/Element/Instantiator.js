import Render from "core/Shared/Structure/Element/Render";
import Manager from "core/Shared/Structure/Element/Manager";
import Editor from "core/Shared/Structure/Element/Editor";

export default class Instantiator {
    instances = {};

    constructor(elementStoreRegistry) {
        this.elementStoreRegistry = elementStoreRegistry;
    }

    instantiator (type) {
        const self = this;

        return {
            manager (props) {
                return self.instance(type, 'manager', props);
            },
            render (props) {
                return self.instance(type, 'render', props);
            },
            editor (props) {
                return self.instance(type, 'editor', props);
            }
        };
    }

    instance (type, segment, props) {
        const elementId = Instantiator.getElementIdByType(props, type);
        const instanceKey = `${type}-${segment}-${elementId}`;

        if (this.instances[instanceKey]) {
            return this.instances[instanceKey];
        }

        const args = [
            elementId,
            type,
            this.elementStoreRegistry,
        ];

        let instance;

        if (segment === 'manager') {
            instance = new Manager(...args);
        } else if (segment === 'editor') {
            instance = new Editor(...args);
        } else if (segment === 'render') {
            instance = new Render(...args);
        }

        return this.instances[instanceKey] = instance;
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
