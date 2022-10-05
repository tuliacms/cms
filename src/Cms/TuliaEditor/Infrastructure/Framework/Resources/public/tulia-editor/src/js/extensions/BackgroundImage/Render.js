export default class Render {
    id;
    block;
    image;
    size;

    constructor (block, image, placement, size) {
        this.validateImage(image);
        let self = this;

        this.block = block;
        this.image = image;
        this.placement = placement ?? 'default';
        this.size = size ?? 'original';
        this.id = block.style({
            'background-image': () => {
                if (!self.image().id) {
                    return `url('#')`;
                }

                return `url('[image_url id="${self.image().id}" size="${this.size}"]')`;
            }
        });
    }

    validateImage (image) {
        if (typeof image !== 'function') {
            throw new Error('Image must be a getter function returning a image.');
        }
    }
}
