const Render = require('shared/Structure/Blocks/Segment/Render.js').default;
const Manager = require('shared/Structure/Blocks/Segment/Manager.js').default;
const Editor = require('shared/Structure/Blocks/Segment/Editor.js').default;

export default class Blocks {
    blocksOptions;
    messenger;
    extensions;

    constructor (blocksOptions, messenger, extensions) {
        this.blocksOptions = blocksOptions;
        this.messenger = messenger;
        this.extensions = extensions;
    }

    editor (props) {
        return new Editor(
            props.block,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger,
            this.extensions
        );
    }

    manager (props) {
        return new Manager(
            props.block,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger,
            this.extensions
        );
    }

    render (props) {
        return new Render(
            props.block,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger,
            this.extensions
        );
    }
}
