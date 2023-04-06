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
        const element = this.getElementByType(props, type);
        const instanceKey = `${type}-${segment}-${element.id}`;

        if (this.instances[instanceKey]) {
            return this.instances[instanceKey];
        }

        const args = [
            element.id,
            element.type,
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

    getElementByType (props, type) {
        switch (type) {
            case 'block': return props.block;
            case 'column': return props.column;
            case 'row': return props.row;
            case 'section': return props.section;
        }
    }
}
