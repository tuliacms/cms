const Render = require('shared/Structure/Element/Render.js').default;
const Manager = require('shared/Structure/Element/Manager.js').default;
const Editor = require('shared/Structure/Element/Editor.js').default;

export default class AbstractElements {
    type;
    options;
    messenger;
    extensions;

    constructor (type, options, messenger, extensions) {
        this.type = type;
        this.options = options;
        this.messenger = messenger;
        this.extensions = extensions;
    }

    editor (props) {
        return new Editor(
            this.type,
            this.getElementByType(props),
            this.getOptionsByType(props),
            this.messenger,
            this.extensions
        );
    }

    manager (props) {
        return new Manager(
            this.type,
            this.getElementByType(props),
            this.getOptionsByType(props),
            this.messenger,
            this.extensions
        );
    }

    render (props) {
        return new Render(
            this.type,
            this.getElementByType(props),
            this.getOptionsByType(props),
            this.messenger,
            this.extensions
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
