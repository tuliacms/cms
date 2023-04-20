import TextBlock from "blocks/TextBlock/TextBlock";
import ImageBlock from "blocks/ImageBlock/ImageBlock";
import VideoBlock from "blocks/VideoBlock/VideoBlock";
import MapBlock from "blocks/MapBlock/MapBlock";
import GalleryBlock from "blocks/GalleryBlock/GalleryBlock";

let blocks = {};

blocks[TextBlock.code] = TextBlock;
blocks[ImageBlock.code] = ImageBlock;
blocks[VideoBlock.code] = VideoBlock;
blocks[MapBlock.code] = MapBlock;
blocks[GalleryBlock.code] = GalleryBlock;

export default blocks;
