export default class Editor {
    constructor(options) {
        this.options = options;
    }

    generatePreviewImagePath(image, size) {
        const imageResolvePath = decodeURIComponent(this.options.filemanager.image_resolve_path);

        return imageResolvePath
            .replace('{size}', size ?? image.size ?? 'original')
            .replace('{id}', image.id)
            .replace('{filename}', image.filename);
    }
}
