export default class Filemanager {
    config;

    constructor (config) {
        this.config = config;
    }

    generatePreviewImagePath (image, size) {
        const imageResolvePath =  decodeURIComponent(this.config.image_resolve_path);

        return imageResolvePath
                .replace('{size}', size ?? image.size ?? 'original')
                .replace('{id}', image.id)
                .replace('{filename}', image.filename);
    }
}
