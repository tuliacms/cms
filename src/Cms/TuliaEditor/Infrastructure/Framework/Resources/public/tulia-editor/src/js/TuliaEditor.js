const VueComponents = require('./shared/VueComponents.js');

module.exports = {
    blocks: [
        require('./blocks/TextBlock.js')
    ],
    extensions: {
        'WysiwygEditor': require('./extensions/WysiwygEditor.js')
    },
    loadExtensions: function () {
        for (let i in TuliaEditor.extensions) {
            let extension = new TuliaEditor.extensions[i]();
            extension.createVueComponent();
        }
    },
    loadBlocks () {
        VueComponents.registerBlocksAsComponents(TuliaEditor.blocks);
    }
};
