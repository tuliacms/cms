const AbstractInstance = require('shared/Extension/Instance/AbstractInstance.js').default;
const _ = require('lodash');

export default class Editor extends AbstractInstance {
    constructor (code, messenger) {
        super (code, `${code}-${_.uniqueId()}`, messenger);

        this.mount();
    }

    mount () {
        this.messenger.execute('extention.mount', { code: this.code, instance: this.instance });
    }

    unmount () {
        this.messenger.execute('extention.unmount', { code: this.code, instance: this.instance });
    }
}
