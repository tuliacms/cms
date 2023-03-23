export default class Assets {
    assets = {};

    require (ownerId, asset) {
        if (!this.assets[ownerId]) {
            this.assets[ownerId] = [];
        }

        this.assets[ownerId].push(asset);
    }

    remove (ownerId, asset) {
        if (!this.assets[ownerId]) {
            return;
        }

        const index = this.assets[ownerId].indexOf(asset);

        if (index >= 0) {
            this.assets[ownerId].splice(index, 1);
        }

        if (this.assets[ownerId].length === 0) {
            delete this.assets[ownerId];
        }
    }

    collect () {
        return this.assets;
    }

    collectNames () {
        let assetsNames = [];

        for (let i in this.assets) {
            assetsNames = assetsNames.concat(this.assets[i]);
        }

        return assetsNames.filter(function (value, index, self) {
            return self.indexOf(value) === index;
        });
    }
}
