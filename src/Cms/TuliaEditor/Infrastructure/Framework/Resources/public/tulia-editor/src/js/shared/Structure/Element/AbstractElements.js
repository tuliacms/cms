const Render = require('shared/Structure/Element/Render.js').default;
const Manager = require('shared/Structure/Element/Manager.js').default;
const Editor = require('shared/Structure/Element/Editor.js').default;

export default class AbstractElements {
    type;
    options;
    messenger;
    extensions;
    childrenManager;
    instances = {
        editor: [],
        manager: [],
        render: [],
    };

    constructor (type, options, messenger, extensions, childrenManager) {
        this.type = type;
        this.options = options;
        this.messenger = messenger;
        this.extensions = extensions;
        this.childrenManager = childrenManager;
    }

    editor (props) {
        const element = this.getElementByType(props);

        if (this.instances.editor[element.id]) {
            return this.instances.editor[element.id];
        }

        return this.instances.editor[element.id] = new Editor(
            this.type,
            element,
            this.getOptionsByType(props),
            this.messenger,
            this.extensions,
            this.childrenManager
        );
    }

    manager (props) {
        const element = this.getElementByType(props);

        if (this.instances.manager[element.id]) {
            return this.instances.manager[element.id];
        }

        return this.instances.manager[element.id] = new Manager(
            this.type,
            element,
            this.getOptionsByType(props),
            this.messenger,
            this.extensions,
            this.childrenManager
        );
    }

    render (props) {
        const element = this.getElementByType(props);

        if (this.instances.render[element.id]) {
            return this.instances.render[element.id];
        }

        return this.instances.render[element.id] = new Render(
            this.type,
            element,
            this.getOptionsByType(props),
            this.messenger,
            this.extensions,
            this.childrenManager
        );
    }

    getOptionsByType (props) {
        switch (this.type) {
            case 'block': return this.options[props.block.code] ?? {};
            case 'column': return this.options['column'] ?? {};
            case 'row': return this.options['row'] ?? {};
            case 'section': return this.options['section'] ?? {};
        }
    }

    getElementByType (props) {
        switch (this.type) {
            case 'block': return props.block;
            case 'column': return props.column;
            case 'row': return props.row;
            case 'section': return props.section;
        }
    }
}
