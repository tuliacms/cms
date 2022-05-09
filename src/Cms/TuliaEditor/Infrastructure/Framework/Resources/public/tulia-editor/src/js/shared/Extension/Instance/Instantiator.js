const Manager = require('shared/Extension/Instance/Manager.js').default;
const Editor = require('shared/Extension/Instance/Editor.js').default;

export default class Instantiator {
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
