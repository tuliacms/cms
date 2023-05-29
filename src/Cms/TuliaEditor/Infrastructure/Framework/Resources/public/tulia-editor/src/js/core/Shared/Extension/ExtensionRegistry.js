export default class ExtensionRegistry {
    constructor(container, extensions) {
        this.container = container;
        this.extensions = extensions;
    }

    editor(name) {
        return this._getServiceOrValue(this.extensions[name].Editor ?? null, name, 'editor');
    }

    manager(name) {
        return this._getServiceOrValue(this.extensions[name].Manager ?? null, name, 'manager');
    }

    render(name) {
        return this._getServiceOrValue(this.extensions[name].Render ?? null, name, 'render');
    }

    _getServiceOrValue(value, name, segment) {
        if (value === null) {
            throw new Error(`Cannot find '${name}' extension for ${segment} segment.`);
        }

        if (typeof value === 'string' || value instanceof String) {
            return this.container.get(value);
        }

        return value;
    }
}
