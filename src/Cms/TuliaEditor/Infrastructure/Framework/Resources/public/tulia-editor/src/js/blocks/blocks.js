const TextBlock = require('./TextBlock/TextBlock.js').default;
const ImageBlock = require('./ImageBlock/ImageBlock.js').default;
const VideoBlock = require('./VideoBlock/VideoBlock.js').default;
const MapBlock = require('./MapBlock/MapBlock.js').default;
const GalleryBlock = require('./GalleryBlock/GalleryBlock.js').default;

let blocks = {};

//blocks[TextBlock.code] = TextBlock;
//blocks[ImageBlock.code] = ImageBlock;
//blocks[VideoBlock.code] = VideoBlock;
//blocks[MapBlock.code] = MapBlock;
blocks[GalleryBlock.code] = GalleryBlock;

export default blocks;
