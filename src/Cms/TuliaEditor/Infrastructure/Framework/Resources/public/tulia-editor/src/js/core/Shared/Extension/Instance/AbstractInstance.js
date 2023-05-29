export default class AbstractInstance {
    code;
    instance;
    messenger;

    constructor (code, instanceId, messenger) {
        this.code = code;
        this.instanceId = instanceId;
        this.messenger = messenger;
    }

    send (operation, data) {
        this.messenger.send(this.generatePrefix(operation), data);
    }

    receive (operation, callable) {
        this.messenger.receive(this.generatePrefix(operation), callable);
    }

    generatePrefix (operation) {
        return `ext.operation.${this.instanceId}.${operation}`;
    }
}
