const { isReactive } = require('vue');

export default class Render {
    id;
    block;
    image;

    constructor (block, image, placement) {
        this.validateImage(image);

        this.block = block;
        this.image = image;
        this.placement = placement ?? 'default';
        this.id = block.style({
            'background-image': () => `url('[image_url id="${image.id}" size="thumbnail"]')`
        })
    }

    validateImage (image) {
        if (image.hasOwnProperty('id') === false || image.hasOwnProperty('filename') === false) {
            throw new Error('Image object must contains "id" and "filename" properties.');
        }

        if (isReactive(image) === false) {
            throw new Error('Image object must be reactive.');
        }
    }
}
