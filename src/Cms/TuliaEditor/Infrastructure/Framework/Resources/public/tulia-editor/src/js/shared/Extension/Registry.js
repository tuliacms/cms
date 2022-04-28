export default class Registry {
    extensions;

    constructor (extensions) {
        this.extensions = extensions;
    }

    get (name) {
        return this.extensions[name];
    }
}
