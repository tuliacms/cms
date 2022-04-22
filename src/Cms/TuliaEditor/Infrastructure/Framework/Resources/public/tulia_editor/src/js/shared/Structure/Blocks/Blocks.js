const Block = require('shared/Structure/Blocks/Editor/Block.js').default;
const Data = require('shared/Structure/Blocks/Data.js').default;

export default class Blocks {
    blocksOptions;
    messenger;

    constructor (blocksOptions, messenger) {
        this.blocksOptions = blocksOptions;
        this.messenger = messenger;
    }

    editor (code, props) {
        return new Block(
            props.id,
            code,
            new Data(props.id, 'editor', props, this.messenger),
            this.blocksOptions[code] ?? {},
            this.messenger
        );
    }

    manager (code, props) {
        return new Block(
            props.id,
            code,
            new Data(props.id, 'manager', props, this.messenger),
            this.blocksOptions[code] ?? {},
            this.messenger
        );
    }

    render (code, props) {
        return new Block(
            props.id,
            code,
            new Data(props.id, 'render', props, this.messenger),
            this.blocksOptions[code] ?? {},
            this.messenger
        );
    }
}
