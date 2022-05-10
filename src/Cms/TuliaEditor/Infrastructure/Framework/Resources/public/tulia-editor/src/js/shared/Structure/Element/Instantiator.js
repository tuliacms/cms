const Render = require('shared/Structure/Element/Render.js').default;
const Manager = require('shared/Structure/Element/Manager.js').default;
const Editor = require('shared/Structure/Element/Editor.js').default;

export default class ElementsInstantiator {
    options;
    messenger;
    extensions;
    structureManipulator;
    instances = {};

    constructor (options, messenger, extensions, structureManipulator) {
        this.options = options;
        this.messenger = messenger;
        this.extensions = extensions;
        this.structureManipulator = structureManipulator;
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

    structureTraversator (type, id, segment) {
        const self = this;

        return {
            getParent () {
                const parent = self.structureManipulator.findParent(id);
                let props = {};
                props[parent.type] = parent;

                return self.instance(parent.type, segment, props);
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
            type,
            element,
            this.getOptionsByType(props, type),
            this.messenger,
            this.extensions,
            this.structureTraversator(type, element.id, segment)
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

    getOptionsByType (props, type) {
        switch (type) {
            case 'block': return this.options.blocks[props.block.code] ?? {};
            case 'column': return this.options.columns ?? {};
            case 'row': return this.options.rows ?? {};
            case 'section': return this.options.sections ?? {};
        }
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
