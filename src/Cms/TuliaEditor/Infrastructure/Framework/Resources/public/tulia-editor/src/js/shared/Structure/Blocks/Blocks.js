const Render = require('shared/Structure/Blocks/Segment/Render.js').default;
const Manager = require('shared/Structure/Blocks/Segment/Manager.js').default;
const Editor = require('shared/Structure/Blocks/Segment/Editor.js').default;
const Data = require('shared/Structure/Blocks/Data.js').default;
const ElementStyle = require('shared/Structure/Style/ElementStyle.js').default;

export default class Blocks {
    blocksOptions;
    messenger;

    constructor (blocksOptions, messenger) {
        this.blocksOptions = blocksOptions;
        this.messenger = messenger;
    }

    editor (props) {
        return new Editor(
            props.block,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger
        );
    }

    manager (props) {
        return new Manager(
            props.block,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger
        );
    }

    render (props) {
        return new Render(
            props.block,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger
        );
    }
}
