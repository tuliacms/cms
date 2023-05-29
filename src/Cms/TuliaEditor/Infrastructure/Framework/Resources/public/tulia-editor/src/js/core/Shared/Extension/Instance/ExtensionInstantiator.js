import Manager from "core/Shared/Extension/Instance/Manager";
import Editor from "core/Shared/Extension/Instance/Editor";

export default class ExtensionInstantiator {
    messenger;

    constructor (messenger) {
        this.messenger = messenger;
    }

    manager (code, instanceId) {
        if (!instanceId) {
            throw new Error(`Please provide an instanceId for '${code}' extension.`);
        }

        return new Manager(code, instanceId, this.messenger);
    }

    editor (code) {
        return new Editor(code, this.messenger);
    }
}
