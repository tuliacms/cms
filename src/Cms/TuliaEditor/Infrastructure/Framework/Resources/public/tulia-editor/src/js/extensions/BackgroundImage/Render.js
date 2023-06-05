export default class Render {
    constructor(filemanagerRender) {
        this.filemanagerRender = filemanagerRender;
    }

    of(block, image) {
        return new RenderOf(this.filemanagerRender, block, image);
    }
}

class RenderOf {
    constructor (filemanagerRender, block, image) {
        this.filemanagerRender = filemanagerRender;
        this.validateImage(image);

        this.block = block;
        this.image = image;
    }

    get link() {
        const img = this.image();

        if (!img.id) {
            return `#`;
        }

        return this.filemanagerRender.generatePreviewImagePath(img);
    }

    get backgroundImage() {
        return `url(${this.link})`;
    }

    validateImage(image) {
        if (typeof image !== 'function') {
            throw new Error('Image must be a getter function returning a image.');
        }
    }
}
