import ExtensionRegistry from "core/Shared/Extension/ExtensionRegistry";

export default class ExtensionRegistryFactory {
    static create(container, extensions) {
        const result = {};

        for (let i in extensions) {
            if (extensions[i].services) {
                extensions[i].services(container);
            }

            result[i] = {
                Editor: extensions[i].Editor ?? null,
                Manager: extensions[i].Manager ?? null,
                Render: extensions[i].Render ?? null,
            };
        }

        return new ExtensionRegistry(container, result);
    }
}
