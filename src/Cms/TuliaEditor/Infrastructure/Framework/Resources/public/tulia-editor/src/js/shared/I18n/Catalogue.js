export default class Catalogue {
    translations = {};

    constructor (translations) {
        this.translations = translations;
    }

    get (name) {
        return this.translations[name] ?? null;
    }
};
