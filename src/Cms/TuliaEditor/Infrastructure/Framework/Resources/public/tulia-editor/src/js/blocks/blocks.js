const TextBlock = require('./TextBlock/TextBlock.js').default;
const ImageBlock = require('./ImageBlock/ImageBlock.js').default;
const VideoBlock = require('./VideoBlock/VideoBlock.js').default;

let blocks = {};

blocks[TextBlock.code] = TextBlock;
blocks[ImageBlock.code] = ImageBlock;
blocks[VideoBlock.code] = VideoBlock;

export default blocks;
