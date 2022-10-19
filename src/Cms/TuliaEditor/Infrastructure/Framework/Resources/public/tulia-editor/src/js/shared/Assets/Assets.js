export default class Assets {
    assets = [];

    require (asset) {
        this.assets.push(asset);
    }

    collect () {
        return this.assets;
    }
}
