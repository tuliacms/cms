const Block = require('shared/Structure/Blocks/Editor/Block.js').default;
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
        return new Block(
            props.block.id,
            props.block.code,
            new Data(props.block.id, 'editor', props.block.data, this.messenger),
            null,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger
        );
    }

    manager (props) {
        return new Block(
            props.block.id,
            props.block.code,
            new Data(props.block.id, 'manager', props.block.data, this.messenger),
            null,
            this.blocksOptions[props.block.code] ?? {},
            this.messenger
        );
    }

    render (props) {
        return new Block(
            props.block.id,
            props.block.code,
            new Data(props.block.id, 'render', props.block.data, this.messenger),
            new ElementStyle(props.block.style),
            this.blocksOptions[props.block.code] ?? {},
            this.messenger
        );
    }
}
