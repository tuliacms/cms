import AbstractInstance from "core/Shared/Extension/Instance/AbstractInstance";
import _ from "lodash";

export default class Editor extends AbstractInstance {
    constructor (code, messenger) {
        super (code, `${code}-${_.uniqueId()}`, messenger);

        this.mount();
    }

    mount () {
        this.messenger.send('extension.mount', { code: this.code, instanceId: this.instanceId });
    }

    unmount () {
        this.messenger.send('extension.unmount', { code: this.code, instanceId: this.instanceId });
    }
}
