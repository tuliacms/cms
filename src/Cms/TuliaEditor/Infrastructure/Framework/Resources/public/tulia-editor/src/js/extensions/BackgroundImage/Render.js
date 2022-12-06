export default class Render {
    id;
    block;
    image;

    constructor (block, image) {
        this.validateImage(image);
        let self = this;

        this.block = block;
        this.image = image;
        this.id = block.style({
            'background-image': () => {
                const img = self.image();

                if (!img.id) {
                    return `url('#')`;
                }

                img.size = img.size ?? 'original';

                return `url('[image_url id="${img.id}" size="${img.size}"]')`;
            }
        });
    }

    link = () => {
        const img = this.image();

        if (!img.id) {
            return `#`;
        }

        return `[image_url id="${img.id}" size="${img.size}"]`;
    };

    validateImage (image) {
        if (typeof image !== 'function') {
            throw new Error('Image must be a getter function returning a image.');
        }
    }
}
