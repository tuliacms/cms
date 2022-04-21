const TextBlock = require('./TextBlock/TextBlock.js').default;
const ImageBlock = require('./ImageBlock/ImageBlock.js').default;

let blocks = {};

blocks[TextBlock.code] = TextBlock;
blocks[ImageBlock.code] = ImageBlock;

export default blocks;
