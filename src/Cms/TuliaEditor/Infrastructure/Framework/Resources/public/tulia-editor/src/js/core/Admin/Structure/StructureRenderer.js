export default class StructureRenderer {
    constructor(assets) {
        this.assets = assets;
    }

    setContent(content) {
        this.content = content;
    }

    render() {
        let content = this.content;
        const assets = this.assets.collectNames();

        if (assets.length) {
            content += `[assets names="${assets.join(',')}"]`;
        }

        return content;
    }
}
