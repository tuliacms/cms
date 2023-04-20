export default class ExtensionRegistry {
    constructor(container, extensions) {
        this.container = container;
        this.extensions = extensions;
    }

    editor(name) {
        return this._getServiceOrValue(this.extensions[name].Editor ?? null);
    }

    manager(name) {
        return this._getServiceOrValue(this.extensions[name].Manager ?? null);
    }

    render(name) {
        return this._getServiceOrValue(this.extensions[name].Render ?? null);
    }

    _getServiceOrValue(value) {
        if (typeof value === 'string' || value instanceof String) {
            return this.container.get(value);
        }

        return value;
    }
}
