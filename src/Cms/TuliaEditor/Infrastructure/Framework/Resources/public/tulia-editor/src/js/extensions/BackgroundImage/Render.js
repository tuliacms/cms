export default class Render {
    id;
    block;
    image;

    constructor (block, image, placement) {
        this.validateImage(image);
        let self = this;

        this.block = block;
        this.image = image;
        this.placement = placement ?? 'default';
        this.id = block.style({
            'background-image': () => {
                if (!self.image().id) {
                    return `url('#')`;
                }

                return `url('[image_url id="${self.image().id}" size="thumbnail"]')`;
            }
        });
    }

    validateImage (image) {
        if (typeof image !== 'function') {
            throw new Error('Image must be a getter function returning a image.');
        }
    }
}
