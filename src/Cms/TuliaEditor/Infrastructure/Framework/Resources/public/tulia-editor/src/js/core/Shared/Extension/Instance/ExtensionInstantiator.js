import Manager from "core/Shared/Extension/Instance/Manager";
import Editor from "core/Shared/Extension/Instance/Editor";

export default class ExtensionInstantiator {
    messenger;

    constructor (messenger) {
        this.messenger = messenger;
    }

    manager (code, instance) {
        return new Manager(code, instance, this.messenger);
    }

    editor (code) {
        return new Editor(code, this.messenger);
    }
}
