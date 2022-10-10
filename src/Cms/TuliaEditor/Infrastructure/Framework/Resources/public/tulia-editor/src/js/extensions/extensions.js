const WysiwygEditor = require('./WysiwygEditor/WysiwygEditor.js').default;
const Contenteditable = require('./Contenteditable/Contenteditable.js').default;
const Image = require('./Image/Image.js').default;
const BackgroundImage = require('./BackgroundImage/BackgroundImage.js').default;
const FontIcon = require('./FontIcon/FontIcon.js').default;
const Collection = require('./Collection/Collection.js').default;
const CollectionActions = require('./Collection/Actions/Actions.js').default;
const CollectionCarousel = require('./Collection/Carousel/Carousel.js').default;
const DynamicBlock = require('./DynamicBlock/DynamicBlock.js').default;

export default {
    'WysiwygEditor': WysiwygEditor,
    'Contenteditable': Contenteditable,
    'Image': Image,
    'BackgroundImage': BackgroundImage,
    'FontIcon': FontIcon,
    'Collection': Collection,
    'Collection.Actions': CollectionActions,
    'Collection.Carousel': CollectionCarousel,
    'DynamicBlock': DynamicBlock,
}
